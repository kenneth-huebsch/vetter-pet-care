<?php
namespace Craft;


/**
 * EmailUserService
 */
class EmailUserService extends BaseApplicationComponent
{

    /**
     *  Send different type Email to User
     */
    public function sendEmail($post)
    {
        /* Different type of user should send different type of email */

        $email = new EmailModel();
        $emailSettings = craft()->email->getSettings();
        // get plugin settings
        $emailUserSettings = craft()->plugins->getPlugin('emailuser')->getSettings();
        $email->fromEmail = $emailSettings['emailAddress'];
        $email->sender = $emailSettings['emailAddress'];

        switch ($post['emailType']) {
            /* Customer Message */
            case 'custApptConfirmed':
                $email->toEmail = $post['customeremail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array('appointmentDate'=> $post['appointmentDate'],
                        'appointmentLongDate'=> $post['appointmentLongDate'],
                        'appointmentStartTime' => $post['appointmentStartTime'],
                        'customerFirstName'=> $post['customerFirstName'],
                        'StartTime' => $post['StartTime'],
                        'appointmentEndTime' => $post['appointmentEndTime'],
                        'appointmentIdNumber' => $post['appointmentIdNumber'],
                        'customerIdNumber' => $post['customerIdNumber'],
                        'customerStreetAddress1' => $post['customerStreetAddress1'],
                        'customerStreetAddress2' => $post['customerStreetAddress2'],
                        'customerCity' => $post['customerCity'],
                        'customerState' => $post['customerState'],
                        'customerZipCode'=> $post['customerZipCode'],
                        'CustID' => $post['CustID'],
                        ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            case 'custApptReminder':
                $email->toEmail = $post['customeremail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'customerFirstName'=> $post['customerFirstName'],
                        'appointmentLongDate'=> $post['appointmentLongDate'],
                        'appointmentStartTime' => $post['appointmentStartTime'],
                        'StartTime' => $post['StartTime'],
                        'appointmentEndTime' => $post['appointmentEndTime'],
                        'appointmentIdNumber' => $post['appointmentIdNumber'],
                        'customerIdNumber' => $post['customerIdNumber'],
                        'customerStreetAddress1' => $post['customerStreetAddress1'],
                        'customerStreetAddress2' => $post['customerStreetAddress2'],
                        'customerCity' => $post['customerCity'],
                        'customerState' => $post['customerState'],
                        'customerZipCode'=> $post['customerZipCode'],
                        'vetName' => $post['vetName'],
                        'CustID' =>$post['CustID'],
                        ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            case 'custApptCancelledByVet':
                $email->toEmail = $post['customeremail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'customerFirstName'=> $post['customerFirstName'],
                        'appointmentStartTime'=> $post['appointmentStartTime'],
                        'appointmentIdNumber' => $post['appointmentIdNumber'],
                        'customerIdNumber' => $post['customerIdNumber'],
                    ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            case 'custVetReassign':
                /* haven't work on this message yet */
                $email->toEmail = $post['customeremail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'appointmentStartTime'=> $post['appointmentStartTime'],
                        'StartTime'=> $post['StartTime'],
                        'appointmentEndTime'=> $post['appointmentEndTime'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                        'newVeterinarianName'=> $post['newVeterinarianName'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'customerFirstName'=> $post['customerFirstName'],
                    ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            case 'custApptSelfCancelled':
                $email->toEmail = $post['customeremail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'appointmentStartTime' => $post['appointmentStartTime'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                        'customerFirstName'=> $post['customerFirstName'],
                    ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            case 'custFeedback':
                $email->toEmail = $post['customeremail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'customerFirstName'=> $post['customerFirstName'],
                        'vetID'=> $post['vetID'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                        'vid' => $post['vid'],
                        'appointmentIdNumber' => $post['appointmentIdNumber'],
                    ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            case 'custMakeApptSoon':
                $email->toEmail = $post['customeremail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'pets_name'=> $post['pets_name'],
                        'customerFirstName'=> $post['customerFirstName'],
                    ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;

                /* Vet message */
            case 'vetSetUpConfirm':
                $email->toEmail = $post['vetEmail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array('appointmentDate'=> $post['appointmentDate'],
                        'vetEmail'=> $post['vetEmail'],
                        'vetcellPhone'=> $post['vetcellPhone'],
                    ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            case 'vetApptNotification':
                $email->toEmail = $post['vetEmail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array('appointmentDate'=> $post['appointmentDate'],
                        'appointmentLongDate'=> $post['appointmentLongDate'],
                        'StartTime' => $post['StartTime'],
                        'customerFirstName' => $post['customerFirstName'],
                        'customerLastName' => $post['customerLastName'],
                        'appointmentIdNumber' => $post['appointmentIdNumber'],
                        'customerStreetAddress1' => $post['customerStreetAddress1'],
                        'customerStreetAddress2' => $post['customerStreetAddress2'],
                        'customerCity' => $post['customerCity'],
                        'customerIdNumber' => $post['customerIdNumber'],
                        'customerZipCode' => $post['customerZipCode'],
                        'customerState' => $post['customerState'],
                        'vetID' => $post['vetID'],
                        'vetEmail'=> $post['vetEmail'],
                        'vetFirstName' => $post['vetFirstName'],
                        'petlist' => $post['petlist'],
                    ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            case 'vetApptCancellationByVet':
                $email->toEmail = $post['vetEmail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'vetID' => $post['vetID'],
                        'vetFirstName'=> $post['vetFirstName'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'appointmentDate'=> $post['appointmentDate'],
                        'appointmentStartTime'=> $post['appointmentStartTime'],
                        'vetEmail'=> $post['vetEmail'],
                    ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            case 'vetApptCancellationByCust':
                $email->toEmail = $post['vetEmail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'vetFirstName'=> $post['vetFirstName'],
                        'vetLastName'=> $post['vetLastName'],
                        'appointmentStartTime'=> $post['appointmentStartTime'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                        'customerStreetAddress1' => $post['customerStreetAddress1'],
                        'customerStreetAddress2' => $post['customerStreetAddress2'],
                        'customerZipCode' => $post['customerZipCode'],
                        'customerCity' => $post['customerCity'],
                        'customerState' => $post['customerState'],
                        'customerFirstName' => $post['customerFirstName'],
                        'customerLastName' => $post['customerLastName'],
                        'vetID'=> $post['vetID'],
                    ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            case 'vetApptReminder':
                $email->toEmail = $post['vetEmail'];
                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'appointmentStartTime'=> $post['appointmentStartTime'],
                        'StartTime'=> $post['StartTime'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                        'customerStreetAddress1' => $post['customerStreetAddress1'],
                        'customerStreetAddress2' => $post['customerStreetAddress2'],
                        'customerCity' => $post['customerCity'],
                        'customerState' => $post['customerState'],
                        'customerZipCode' => $post['customerZipCode'],
                        'customerFirstName' => $post['customerFirstName'],
                        'customerLastName' => $post['customerLastName'],
                        'vetID'=> $post['vetID'],
                        'vetFirstName'=> $post['vetFirstName'],
                        'petlist' => $post['petlist'],
                    ));
                $email->body = $body;
                craft()->email->sendEmail($email);
                $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
                break;
            /* Admin Message */
            case 'adminNewCustAcctCreated':
                /* find admin group users */
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array('appointmentDate'=> $post['appointmentDate'],
                        'vetEmail'=> $post['vetEmail'],
                        'vetcellPhone'=> $post['vetcellPhone'],
                    ));
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user['email'];
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }
                break;
            case 'adminNewVetAppForm':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array('appointmentDate'=> $post['appointmentDate'],
                        'vetEmail'=> $post['vetEmail'],
                        'vetcellPhone'=> $post['vetcellPhone'],
                    ));
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user['email'];
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }
                break;
            case 'adminNewVetAcctSetUpCompleted':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array('appointmentDate'=> $post['appointmentDate'],
                        'vetEmail'=> $post['vetEmail'],
                        'vetcellPhone'=> $post['vetcellPhone'],
                    ));
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user['email'];
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }
                break;
            case 'adminApptBookedByCust':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;

                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array('appointmentDate'=> $post['appointmentDate'],
                        'appointmentLongDate'=> $post['appointmentLongDate'],
                        'StartTime' => $post['StartTime'],
                        'customerFirstName'=> $post['customerFirstName'],
                        'customerLastName'=> $post['customerLastName'],
                        'appointmentIdNumber' => $post['appointmentIdNumber'],
                        'customerIdNumber' => $post['customerIdNumber'],
                        'customerStreetAddress1' => $post['customerStreetAddress1'],
                        'customerStreetAddress2' => $post['customerStreetAddress2'],
                        'customerState' => $post['customerState'],
                        'customerCity' => $post['customerCity'],
                        'customerZipCode' => $post['customerZipCode'],
                        'vetID' => $post['vetID'],
                        'vetEmail'=> $post['vetEmail'],
                        'vetcellPhone'=> $post['vetcellPhone'],
                        'vetFirstName' => $post['vetFirstName'],
                        'vetLastName' => $post['vetLastName'],
                        'petlist' => $post['petlist'],
                        'adminNotes' => $post['adminNotes']
                    ));
                /* replace break html tag */
                $body = str_replace('newlineline ', '<br> ', $body);;
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user['email'];
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }
                break;
            case 'adminVetCancelledGreaterThan48hrs':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'vetFirstName'=> $post['vetFirstName'],
                        'vetLastName'=> $post['vetLastName'],
                        'customerFirstName'=> $post['customerFirstName'],
                        'customerLastName'=> $post['customerLastName'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'customerStreetAddress1' => $post['customerStreetAddress1'],
                        'customerStreetAddress2' => $post['customerStreetAddress2'],
                        'customerCity' => $post['customerCity'],
                        'customerState' => $post['customerState'],
                        'customerZipCode' => $post['customerZipCode'],
                        'vetID' => $post['vetID'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                    ));
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user['email'];
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }

                break;
            case 'adminVetCancelledLessThan48hrs':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'vetFirstName'=> $post['vetFirstName'],
                        'vetLastName'=> $post['vetLastName'],
                        'customerFirstName'=> $post['customerFirstName'],
                        'customerLastName'=> $post['customerLastName'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'vetID' => $post['vetID'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                    ));
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user['email'];
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }

                break;
            case 'adminReschCompleted':
                /* haven't work on this message yet */
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'appointmentStartTime'=> $post['appointmentStartTime'],
                        'StartTime'=> $post['StartTime'],
                        'newVeterinarianName'=> $post['newVeterinarianName'],
                        'newVeterinarianID'=> $post['newVeterinarianID'],
                        'customerFirstName'=> $post['customerFirstName'],
                        'customerLastName'=> $post['customerLastName'],
                        'customerStreetAddress1' => $post['customerStreetAddress1'],
                        'customerStreetAddress2' => $post['customerStreetAddress2'],
                        'customerCity' => $post['customerCity'],
                        'customerState' => $post['customerState'],
                        'customerZipCode' => $post['customerZipCode'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                    ));
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user['email'];
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }

                break;
            case 'adminApptCancelledBySys':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'customerName'=> $post['customerName'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                        'customeremail'=> $post['customeremail'],
                    ));
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user['email'];
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }
                break;
            case 'adminApptCancelledByAdmin':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'customerName'=> $post['customerName'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                        'customeremail'=> $post['customeremail'],
                    ));
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user['email'];
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }
                break;
            case 'adminClientCancelledLessThan24Hrs':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'vetFirstName'=> $post['vetFirstName'],
                        'vetLastName'=> $post['vetLastName'],
                        'customerFirstName'=> $post['customerFirstName'],
                        'customerLastName'=> $post['customerLastName'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'vetID' => $post['vetID'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                    ));
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user['email'];
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }

                break;
            case 'adminClientCancelledGreaterThan24Hrs':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'vetFirstName'=> $post['vetFirstName'],
                        'vetLastName'=> $post['vetLastName'],
                        'customerFirstName'=> $post['customerFirstName'],
                        'customerLastName'=> $post['customerLastName'],
                        'appointmentIdNumber'=> $post['appointmentIdNumber'],
                        'vetID' => $post['vetID'],
                        'customerIdNumber'=> $post['customerIdNumber'],
                    ));
                $email->body = $body;

                foreach ($users as $user){
                    $email->toEmail = $user->email;
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }
                break;

            case 'adminNoHoldID':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'appointmentIdNumber' => $post['appointmentIdNumber'],
                    ));
                $email->body = $body;
                foreach ($users as $user){
                    $email->toEmail = $user->email;
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }
                break;

            case 'adminHoldingApptMissing':
                $element = craft()->elements->getCriteria(ElementType::User);
                $element->group = 'adminWNotifications';
                $users = $element->find();

                $EmailSubject = $post['emailType'].'Subject';
                $email->subject = $emailUserSettings->$EmailSubject;
                $body = craft()->templates->renderString($emailUserSettings->$post['emailType'],
                    array(
                        'appointmentIdNumber' => $post['appointmentIdNumber'],
                    ));
                $email->body = $body;

                foreach ($users as $user){
                    $email->toEmail = $user->email;
                    craft()->email->sendEmail($email);
                    $log_message = $EmailSubject . " Call Send Mail. Sent " . $post['emailType'].' '.$email->toEmail.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);
                }
                break;

            default:
                $log_message="No message is sent";
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
        }


    }




}