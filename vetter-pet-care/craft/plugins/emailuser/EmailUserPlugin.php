<?php
/**
 * Update Matrix plugin for Craft CMS
 *
 * This plugin will delete/update Matrix field entires
 *
 *
 *
 * @author    Angela Chan
 * @copyright Copyright (c) 2017 Angela Chan
 * @link
 * @package   UpdateMatrix
 * @since     1.0.0
 */

namespace Craft;

class EmailUserPlugin extends BasePlugin
{
    function getName()
    {
        return Craft::t('Email User');
    }

    function getVersion()
    {
        return '1.0';
    }

    function getDeveloper()
    {
        return 'The Tactile Group';
    }

    function getDeveloperUrl()
    {
        return 'http://www.thetactilegroup.com';
    }

    protected function defineSettings()
    {
        return array(
            'custAccountCreation' => array(AttributeType::String),
            'custAccountCreationSubject' => array(AttributeType::String),
            'custApptConfirmed'=> array(AttributeType::String),
            'custApptConfirmedSubject'=> array(AttributeType::String),
            'custApptReminder'=> array(AttributeType::String),
            'custApptReminderSubject'=> array(AttributeType::String),
            'custApptCancelledByVet'=> array(AttributeType::String),
            'custApptCancelledByVetSubject'=> array(AttributeType::String),
            'custVetReassign'=> array(AttributeType::String),
            'custVetReassignSubject'=> array(AttributeType::String),
            'custApptSelfCancelled'=> array(AttributeType::String),
            'custApptSelfCancelledSubject'=> array(AttributeType::String),
            'custFeedback'=> array(AttributeType::String),
            'custFeedbackSubject'=> array(AttributeType::String),
            'custMakeApptSoon'=> array(AttributeType::String),
            'custMakeApptSoonSubject'=> array(AttributeType::String),
            'vetAccountCreation' => array(AttributeType::String),
            'vetAccountCreationSubject' => array(AttributeType::String),
            'vetApptNotification' => array(AttributeType::String),
            'vetApptNotificationSubject' => array(AttributeType::String),
            'vetApptCancellationByVet' => array(AttributeType::String),
            'vetApptCancellationByVetSubject' => array(AttributeType::String),
            'vetApptCancellationByCust' => array(AttributeType::String),
            'vetApptCancellationByCustSubject' => array(AttributeType::String),
            'vetApptReminder' => array(AttributeType::String),
            'vetApptReminderSubject' => array(AttributeType::String),
            'adminNewCustAcctCreated' => array(AttributeType::String),
            'adminNewCustAcctCreatedSubject' => array(AttributeType::String),
            'adminApptBookedByCust' => array(AttributeType::String),
            'adminApptBookedByCustSubject' => array(AttributeType::String),
            'adminVetCancelledGreaterThan48hrs' => array(AttributeType::String),
            'adminVetCancelledGreaterThan48hrsSubject' => array(AttributeType::String),
            'adminVetCancelledLessThan48hrs' => array(AttributeType::String),
            'adminVetCancelledLessThan48hrsSubject' => array(AttributeType::String),
            'adminReschCompleted' => array(AttributeType::String),
            'adminReschCompletedSubject' => array(AttributeType::String),
            'adminApptCancelledBySys' => array(AttributeType::String),
            'adminApptCancelledByAdminSubject'=> array(AttributeType::String),
            'adminApptCancelledByAdmin' => array(AttributeType::String),
            'adminApptCancelledBySysSubject' => array(AttributeType::String),
            'adminClientCancelledLessThan24Hrs' => array(AttributeType::String),
            'adminClientCancelledLessThan24HrsSubject' => array(AttributeType::String),
            'adminClientCancelledGreaterThan24Hrs' => array(AttributeType::String),
            'adminClientCancelledGreaterThan24HrsSubject' => array(AttributeType::String),
            'adminNoHoldID' => array(AttributeType::String),
            'adminNoHoldIDSubject' => array(AttributeType::String),
            'adminHoldingApptMissing' => array(AttributeType::String),
            'adminHoldingApptMissingSubject' => array(AttributeType::String),
        );
    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('emailuser/settings',array(
                    'settings'=>$this->getSettings()
        ));
    }

//    public function registerSiteRoutes()
//    {
//        return array(
//            'sendEmail' => array('action' => 'emailUser/send_email'),
//        );
//    }

    public function hasCpSection()
    {
        return false;
    }

    public function getRandomString($length = 36, $extendedChars = false)
    {
        return StringHelper::randomString($length, $extendedChars);
    }

    public function init(){
       parent::init();
        //craft()->on('users.onSaveUser',function(Event $event){
            craft()->on('userGroups.onAssignUserToGroups',function(Event $event){

           // $user = $event->params['user'];
               $user_id = $event->params['userId'];
                $user = craft()->users->getUserById($user_id);

            $userId = craft()->request->getPost('userId');
            $isNewUser = !$userId;

            $usergroup = $event->params['groupIds'];

            $email = new EmailModel();
            $emailSettings = craft()->email->getSettings();
            // get plugin settings
            $emailUserSettings = craft()->plugins->getPlugin('emailuser')->getSettings();
            $email->fromEmail = $emailSettings['emailAddress'];
            $email->sender = $emailSettings['emailAddress'];


            if ($isNewUser){
                EmailUserPlugin::log('user group '. $usergroup[0],LogLevel::Info,true);
                if ($usergroup[0] == '1'){

                    $email->subject = $emailUserSettings->custAccountCreationSubject;
                    $body=craft()->templates->renderString($emailUserSettings->custAccountCreation,
                        array(
                            'customerFirstName' => $user['firstName'],
                            'customerLastName' => $user['lastName'],
                            'customerIdNumber' => $user['id'],
                            'userid' => $user['id'],
                        ));
                    $email->body = $body;
                    $email->toEmail = $user->email;
                    craft()->email->sendEmail($email);
                    $log_message="Sent message to ".$user->email.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);

                    /* send admin new account adminNewCustAcctCreated */
                    $element = craft()->elements->getCriteria(ElementType::User);
                    $element->group = 'adminWNotifications';
                    $users = $element->find();

                    $email->subject = $emailUserSettings->adminNewCustAcctCreatedSubject;
                    $body=craft()->templates->renderString($emailUserSettings->adminNewCustAcctCreated,
                        array(
                            'customerfirstName' => $user['firstName'],
                            'customerlastName' => $user['lastName'],
                            'customerIdNumber' => $user['id'],
                            'userid' => $user['id'],
                        ));
                    $email->body = $body;
                    foreach ($users as $user){
                        $email->toEmail = $user['email'];
                        craft()->email->sendEmail($email);
                    }
                    $email->toEmail = 'a.chan@thetactilegroup.com';
                    craft()->email->sendEmail($email);
                    $log_message="Sent message to ".$user->email.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);

                } elseif ($usergroup[0] == '2') {
                    /* send vet account has been created message */
                    $url = craft()->users->getPasswordResetUrl($user);
                    $email->subject = $emailUserSettings->vetAccountCreationSubject;
                    $body = craft()->templates->renderString($emailUserSettings->vetAccountCreation,
                        array('vetfirstName' => $user['firstName'],
                            'vetID' => $user['id'],
                            'password_url' => $url,
                        ));
                    $email->body = $body;
                    $email->toEmail = $user->email;
                    craft()->email->sendEmail($email);
                    $log_message="Sent message to ".$user->email.' '.$body;
                    EmailUserPlugin::log($log_message,LogLevel::Info,true);

                }else {
                    /* user is not vet or customer */
                    EmailUserPlugin::log('create admin or admin /w notification',LogLevel::Info,true);
                }

            }else{
                $log_message = "User is not a new user, no message was sent.";
                EmailUserPlugin::log($log_message,LogLevel::Info,true);
            }




        });
        /* route user to edit profile page */
        craft()->on('users.onBeforeActivateUser', function(Event $event)
        {
            $userId = craft()->request->getPost('userId');
            $isNewUser = !$userId;
            $usergroup = craft()->request->getPost('usergroup');

            if ($isNewUser){
                if ($usergroup == 'customer') {
                    $user = $event->params['user'];
                    craft()->config->set('activateAccountSuccessPath', 'user/' . $user->id . '/basic-info');
                }
                else{
                    /* No need to route veterinarian to any page if it create by admin */
                }
            }
        });

    }

}