<?php
/**
 * Created by PhpStorm.
 * User: achan90
 * Date: 8/14/17
 * Time: 5:51 PM
 */

namespace craft;


class TwilioSmsService extends BaseApplicationComponent
{

    private function format_number($number, $addplus = true) {
        // remove all non-number characters from the string
        $number = preg_replace('/[^0-9,]|,[0-9]*$/', '', $number) . "\r\n";
        $number = $addplus ? '+' . $number : $number;
        return $number;
    }

    // detect if the request is ajax
    private function is_ajax() {
        // check the HTTP_X_REQUESTED_WITH header
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // request is ajax
            return true;
        }

        // request is not ajax
        return false;
    }

    public function SendSms($post){
        // require POST
        //$this->requirePostRequest();

        // get the plugin settings
        $settings = craft()->plugins->getPlugin('twilioSms')->getSettings()->attributes;

        // Modify to get customer cell phone
        $_POST['sms_user_phone'] = $post['TextTo'];

        // format the phone number (trim and remove non-numeric characters)
        $number   = trim($this->format_number($_POST['sms_user_phone'], false));

        // format the message
        // $message  = trim($_POST['sms_message']);

        if ($post['textType'] == 'custApptReminderTxt') {
            $message = $settings['custApptReminderTxt'];
        } elseif ($post['textType'] == 'custApptCancelledTxt'){
            $message = $settings['custApptCancelledTxt'];
        } elseif ($post['textType'] == 'vet24hrsApptNotificationTxt'){
            $message = $settings['vet24hrsApptNotificationTxt'];
        }


        // validation error(s)
        $errors   = array();

        // include the Twilio REST API
        require_once(CRAFT_PLUGINS_PATH . 'twiliosms/resources/php/Twilio.php');

        // make sure the number was not empty
        if(!strlen($number)) {
            $errors[] = $settings['numberMissingMsg'];
            craft()->userSession->setFlash('phone', $settings['numberMissingMsg']);
            // make sure number is greater than 5 digits
            // 5 digits is a valid length for a phone number in the Solomon Islands
        } else if(strlen($number) < 5) {
            $errors[] = $settings['numberShortMsg'];
            craft()->userSession->setFlash('phone', $settings['numberShortMsg']);
            // the phone number entered isn't a number
        } else if(!is_numeric($number)) {
            $errors[] = $settings['numberInvalidMsg'];
            craft()->userSession->setFlash('phone', $settings['numberInvalidMsg']);
        }

        // the message was empty
        if(!strlen($message)) {
            $errors[] = $settings['messageMissingMsg'];
            craft()->userSession->setFlash('message', $settings['messageMissingMsg']);
        }

        // there were errors. exit and output them
        if(!empty($errors)) {
            // if the admin post setting is ajax
            if($settings['ajaxOrRedirect'] === 'ajax') {
                exit('{"success": false, "errors": ' . json_encode($errors) . '}');
            }

            // there were errors, let's retain their input
            craft()->userSession->setFlash('user_number',  $_POST['sms_user_phone']);
            craft()->userSession->setFlash('user_message', $_POST['sms_message']);
            // no errors, send the sms
        } else {
            // Twilio-assigned number
            $from  = $settings['from'];
            // numbers to send to, made into an array delimited by line breaks
            //$toArr = explode("\r\n", $settings['to']);
            // 08-07-2017 achan need to use dynamic "to" phone number
            if ($post['TextTo'] == ''){
                $toArr = explode("\r\n", $settings['to']);
                TwilioSmsPlugin::log($toArr[0],LogLevel::Info,true);
            }else{
                $toArr = explode("\r\n", $post['TextTo']);
                $logmessage = 'Send sms message to '.$toArr[0].' '.$post['textType'].' from service in Twilio plugin.';
                TwilioSmsPlugin::log($logmessage,LogLevel::Info,true);
            }


            // sms message body
            $msg  = $settings['msgPrefix'] . "\r\n"; // prefix
            $msg .= "-\r\n";
            $msg .= $message . "\r\n";               // message
            //$msg .= $number . "\r\n";                // the number the user filled in the form
            $msg .= "-\r\n";
            $msg .= $settings['msgPostfix'];         // postfix

            // set SID and AuthToken
            $sid  = $settings['sid'];
            $auth = $settings['authToken'];

            // create a Twilio REST client instance
            $twilio = new \Services_Twilio($sid, $auth);

            // send the message to each number
            foreach($toArr as $idx => $to) {
                $twilio->account->sms_messages->create($from, $to, $msg);
            }

            // if admin post setting is redirect and not ajax
            if($settings['ajaxOrRedirect'] === 'redirect' && trim($settings['redirect']) !== '') {
                // redirect
                header('Location: ' . $settings['redirect']);
            }

            // if we made it here, it sent successfully
            echo '{"success": true, "msg": "' . $settings['successMsg'] . '"}';
            // exit to prevent HTML rendering
           /*  exit;  achan 08-15-2017 comment this out to trigger more than 1 message */
        }
    }

}