<?php
/**
 * Displaced Appointments plugin for Craft CMS
 *
 * DisplacedAppointments_Appointments Controller
 *
 * @author    Matthew Cieslak
 * @copyright Copyright (c) 2017 Matthew Cieslak
 * @link      https://github.com/mattman93
 * @package   DisplacedAppointments
 * @since     1.0.0
 */

namespace Craft;

class DisplacedAppointmentsController extends BaseController
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = true;

  
   public function actionEntrySubmit(){
    
    $this->requireAjaxRequest();
    $charged = false;
    $entryid = craft()->request->getPost('entryid'); 
    $status = craft()->request->getPost('status');  
    $start = craft()->request->getPost('start');
    $vetid = craft()->request->getPost('vetid');

    $entry = craft()->entries->getEntryById($entryid);

       $appointmentStart = new DateTime($entry->appointmentStartTime);
       $now = new DateTime("now");
       $tz =  new \DateTimeZone('America/New_York');
       $now = $now->setTimeZone($tz);
       $hourDiff = $appointmentStart->diff($now);
       $hrs = $hourDiff->format("%H");
       $hrs = $hrs + (($hourDiff->format("%d"))*24);


    if($entry->getContent()->cancel_penalty != "true"){

        EmailUserPlugin::log('EntrySubmit : '.$hrs, LogLevel::Info, true);
          if($hrs < 48){
            $entry->setContentFromPost(['cancel_penalty' => "true"]); 
            $charged = true;
             }
         }else{
        if($hrs <= 48){
            $charged = true;
        }else{
            $charged = false;
        }
    }

    $entry->setContentFromPost(['cancel_status' => $status]); 
    craft()->entries->saveEntry($entry);

                   if (substr($entry->getContent()->assignedVeterinarianIdNumber,0,1) == 'V'){
                        $vetid_num= substr($entry->getContent()->assignedVeterinarianIdNumber, 3);
                   }else{
                       $vetid_num = $entry->getContent()->assignedVeterinarianIdNumber;
                   }
                  $user = craft()->users->getUserById($vetid_num);
                   $first = $user->firstName;
                   $last = $user->lastName;
                  $vetEmail = $user->username;
                  $vetCell = $user->cellPhone;


                 $post_admin['vetFirstName']= $first;
                 $post_admin['vetLastName']= $last;
                 $customerFirst = explode(" ", $entry->getContent()->customerName)[0];
                 $customerLast = explode(" ", $entry->getContent()->customerName)[1];
                 $post_admin['customerFirstName']= $customerFirst;
                 $post_admin['customerLastName']= $customerLast;

                 $post_admin['customerIdNumber']= $entry->getContent()->customerIdNumber;
                 $post_admin['appointmentIdNumber']= $entry->getContent()->appointmentIdNumber;
                 $post_admin['customerStreetAddress1'] = $entry->getContent()->customerStreetAddress1;
                 $post_admin['customerStreetAddress2'] = $entry->getContent()->customerStreetAddress2;
                 $post_admin['customerCity'] = $entry->getContent()->customerCity;
                 $post_admin['customerState'] = $entry->getContent()->customerstate;
                 $post_admin['customerZipCode'] = $entry->getContent()->customerZipCode;

                 
                 $post_admin['vetID']= $entry->getContent()->assignedVeterinarianIdNumber;
                
                   if($charged){
                   $post_admin['emailType']= "adminVetCancelledLessThan48hrs";
                     } else {
                   $post_admin['emailType']= "adminVetCancelledGreaterThan48hrs";
                     }
  
        craft()->emailUser->sendEmail($post_admin);

                $post_vet['vetFirstName']= $first;
                $post_vet['appointmentIdNumber']= $entry->getContent()->appointmentIdNumber;
                $post_vet['appointmentDate']= $start;
                $post_vet['vetID']= $entry->getContent()->assignedVeterinarianIdNumber;
                $post_vet['emailType']= "vetApptCancellationByVet";
                $post_vet['appointmentStartTime'] = $appointmentStart->format('l F d, Y').' at '.$appointmentStart->format(' g:i a');
                $post_vet['vetEmail']= $vetEmail;
                $post_vet['vetcellPhone'] = $vetCell;

        craft()->emailUser->sendEmail($post_vet);
        $log_message = ' vetEmail : '.$vetEmail.'vetCell : '.$vetCell;
       EmailUserPlugin::log($log_message, LogLevel::Info, true);


    $this->returnJson(array('charged' => $charged));
      //craft()->request->redirect(craft()->getSiteUrl().'admin/charge/appointments/processed');
      die();
    }
    public function actionRebook(){
       try{
           $this->requireAjaxRequest();
           $charged = false;
           $mode = craft()->request->getPost('mode');
           $entryid = craft()->request->getPost('entryid');
           $status = craft()->request->getPost('status');
           $vetname = craft()->request->getPost('vetname');
           $vetid = craft()->request->getPost('userid');
           $start = craft()->request->getPost('start');
           $vetuser = craft()->users->getUserById($vetid);
           $log_message = "vetid ".$vetid." entry id: ".$entryid." status: ".$status;
           DisplacedAppointmentsPlugin::log($log_message, LogLevel::Info, true);

           $formattedVetId = "V-0".$vetid;
           $newTitle = "Booking for ".$vetname;
           $entry = craft()->entries->getEntryById($entryid);
           $origin_vet = substr($entry->assignedVeterinarianIdNumber, 3);
           $origin_vet_whole_id = $entry->assignedVeterinarianIdNumber;
           $origin_vet_info = craft()->users->getUserById($origin_vet);
           /* get customer info */
           $uid = $entry->getContent()->customerIdNumber;
           $custid = explode('-',$uid);
           $customeruser = craft()->users->getUserById($custid[2]);


           $appointmentStart = new DateTime($entry->appointmentStartTime);
           $now = new DateTime("now");
           $tz =  new \DateTimeZone('America/New_York');
           $now = $now->setTimeZone($tz);
           $hourDiff = $now->diff($appointmentStart);
           $hrs = $hourDiff->h;
           $hrs = $hrs + ($hourDiff->d*24);
           if (($hourDiff->i > 0) || ($hourDiff->s > 0)){
               $hrs = $hrs +1;
           }
           DisplacedAppointmentsPlugin::log('rebook : '.$hourDiff->h. " ".$hourDiff->d*24, LogLevel::Info, true);
           DisplacedAppointmentsPlugin::log('rebook : total'.$hrs, LogLevel::Info, true);


           if($entry->getContent()->cancel_penalty != "true"){
               if($hrs <= 48){
                   $entry->setContentFromPost(['cancel_penalty' => "true"]);
                   $charged = true;
               }
           } else {
               if($hrs <= 48){
                   $charged = true;
               }else{
                   $charged = false;
               }
           }

           $authorMatrix = craft()->fields->getFieldByHandle("authors_list");
           $block = new MatrixBlockModel();
           $block->fieldId    = $authorMatrix->id; // Matrix field's ID
           $block->ownerId    = $entryid; // ID of entry the block should be added to
           $authorMatrix = craft()->matrix->getBlockTypesByFieldId($authorMatrix->id);
           foreach($authorMatrix as $key => $data){
               if($data->handle === "authors_list"){
                   $authorBlock = $data;
                   break;
               };
           };
           $block->typeId     =  $authorBlock->id; // ID of block type
           $block->getContent()->setAttributes(array(
               'author_name' => $entry->getContent()->assignedVeterinarianName,
               'user_id' => $origin_vet
           ));
           $success = craft()->matrix->saveBlock($block);


           /* send vetApptCancellationByVet notification to exiting vet, only send when it is auto book,
           * Not send it when cancel status is unavailable on manual rebook
           */
           if($entry->getContent()->cancel_status != "unavailable") {
               $post_origin_vet['vetFirstName'] = $origin_vet_info->firstName;
               $post_origin_vet['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
               $appointmentStart = new DateTime($entry->appointmentStartTime);
               $post_origin_vet['appointmentStartTime'] = $appointmentStart->format('l F d, Y').' at '.$appointmentStart->format(' g:i a');

               $post_origin_vet['appointmentDate'] = $start;
               $post_origin_vet['vetID'] = $origin_vet_whole_id;
               $post_origin_vet['emailType'] = "vetApptCancellationByVet";
               $post_origin_vet['vetEmail'] = $origin_vet_info->username;
               $post_origin_vet['vetcellPhone'] = $origin_vet_info->cellPhone;

               craft()->emailUser->sendEmail($post_origin_vet);
               $log_message = "Send vet appt cancellation by vet " . $entry->getContent()->appointmentIdNumber . ' ' . $post_origin_vet['emailType'];
               EmailUserPlugin::log($log_message, LogLevel::Info, true);

               /* send initial cancellation email to admin */
               $post_admin_cancel['vetFirstName']= $origin_vet_info->firstName;
               $post_admin_cancel['vetLastName']= $origin_vet_info->lastName;
               $post_admin_cancel['customerFirstName']= $customeruser->firstName;
               $post_admin_cancel['customerLastName']= $customeruser->lastName;
               $post_admin_cancel['customerStreetAddress1'] = $entry->getContent()->customerStreetAddress1;
               $post_admin_cancel['customerStreetAddress2'] = $entry->getContent()->customerStreetAddress2;
               $post_admin_cancel['customerCity'] = $entry->getContent()->customerCity;
               $post_admin_cancel['customerState'] = $entry->getContent()->customerstate;
               $post_admin_cancel['customerZipCode'] = $entry->getContent()->customerZipCode;
               $post_admin_cancel['customerIdNumber']= $entry->getContent()->customerIdNumber;
               $post_admin_cancel['appointmentIdNumber']= $entry->getContent()->appointmentIdNumber;

               $post_admin_cancel['vetID']= $origin_vet_whole_id;

               if($charged){
                   $post_admin_cancel['emailType']= "adminVetCancelledLessThan48hrs";
               } else {
                   $post_admin_cancel['emailType']= "adminVetCancelledGreaterThan48hrs";
               }
               craft()->emailUser->sendEmail($post_admin_cancel);
               $log_message = "Send cancellation email to admin ".$entry->getContent()->appointmentIdNumber.' '.$post_admin_cancel['emailType'];
               EmailUserPlugin::log($log_message, LogLevel::Info, true);
           }


           $entry->getContent()->title = $newTitle;
           $entry->setContentFromPost(['title' => $newTitle,
               'assignedVeterinarianIdNumber' => $formattedVetId,
               'assignedVeterinarianName' => $vetname,
               'cancel_status' => "rebooked"]);

           craft()->entries->saveEntry($entry);

           //update hold entry with new vet email
           //get new vet email
           $new_vet = craft()->users->getUserById($vetid);
           $new_vet_email = $new_vet->username;
           //update hold entry with new vet email
           if($entry->holdEntryId != 0){
               $hold_entry_id = $entry->holdEntryId;
               $hold_entry = craft()->entries->getEntryById($hold_entry_id);
               $hold_entry->setContentFromPost(['vetemail' => $new_vet_email]);
               craft()->entries->saveEntry($hold_entry);
           }


           /* send new schedule to admin */
           $post_admin['customeremail'] = $customeruser->email;
           $post_admin['newVeterinarianName'] = $vetname;
           $post_admin['newVeterinarianID'] = $formattedVetId;
           $starttime = new DateTime($entry->appointmentStartTime);
           $post_admin['appointmentStartTime'] = $starttime->format('Y-m-d g:i a');
           $post_admin['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
           $post_admin['customerFirstName'] = $customeruser->firstName;
           $post_admin['customerLastName'] = $customeruser->lastName;
           $post_admin['customerStreetAddress1'] = $entry->getContent()->customerStreetAddress1;
           $post_admin['customerStreetAddress2'] = $entry->getContent()->customerStreetAddress2;
           $post_admin['customerCity'] = $entry->getContent()->customerCity;
           $post_admin['customerState'] = $entry->getContent()->customerstate;
           $post_admin['customerZipCode'] = $entry->getContent()->customerZipCode;
           $post_admin['customerIdNumber'] = $uid;
           $post_admin['StartTime'] = $starttime->format('g:i a');
           $post_admin['appointmentEndTime'] = $starttime->modify('+90 minutes');
           $post_admin['emailType']= "adminReschCompleted";
           craft()->emailUser->sendEmail($post_admin);
           $log_message = "Send new schedule to admin ".$entry->getContent()->appointmentIdNumber.' '.$post_admin['emailType'];
           EmailUserPlugin::log($log_message, LogLevel::Info, true);


           $post_newvet['appointmentDate'] =  $start;
           $appointmentStart = new DateTime($entry->appointmentStartTime);
           $post_newvet['appointmentLongDate'] =  $appointmentStart->format('l F d, Y');
           $post_newvet['StartTime'] =  $appointmentStart->format('g:i a');
           $post_newvet['appointmentIdNumber'] =  $entry->appointmentIdNumber;
           $post_newvet['customerFirstName'] = $customeruser->firstName;
           $post_newvet['customerLastName'] = $customeruser->lastName;
           $post_newvet['customerStreetAddress1'] = $entry->getContent()->customerStreetAddress1;
           $post_newvet['customerStreetAddress2'] = $entry->getContent()->customerStreetAddress2;
           $post_newvet['customerCity'] = $entry->getContent()->customerCity;
           $post_newvet['customerState'] = $entry->getContent()->customerstate;
           $post_newvet['customerZipCode'] = $entry->getContent()->customerZipCode;
           $post_newvet['customerIdNumber'] =  $entry->customerIdNumber;
           $post_newvet['vetEmail'] =  $vetuser->email;
           $post_newvet['vetcellPhone'] =  $vetuser->cellPhone;
           $post_newvet['vetFirstName'] =  $vetuser->firstName;
           $post_newvet['vetID'] =  $formattedVetId;
           $post_newvet['emailType'] =  'vetApptNotification';

           craft()->emailUser->sendEmail($post_newvet);
           $log_message = "Send new schedule to new vet ".$entry->getContent()->appointmentIdNumber.' '.$post_newvet['emailType'];
           EmailUserPlugin::log($log_message, LogLevel::Info, true);

           /* send new assign vet info to customer , but only fire email when start time and now is between 24 hours */

           $post['customeremail'] = $customeruser->email;
           $post['newVeterinarianName'] = $vetname;
           $post['newVeterinarianID'] = $formattedVetId;
           $starttime = new DateTime($entry->appointmentStartTime);
           $post['appointmentStartTime'] = $starttime->format('Y-m-d g:i a');
           $post['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
           $post['customerFirstName'] = $customeruser->firstName;
           $post['customerIdNumber'] = $uid;
           $post['StartTime'] = $starttime->format('g:i a');
           $appointmentEndTime = $starttime->modify('+90 minutes');
           $post['appointmentEndTime'] =$appointmentEndTime->format('g:i a');
           $post['emailType']= "custVetReassign";
           /* for text message */
           $post['textType'] = "vet24hrsApptNotificationTxt";
           $user = craft()->users->getUserById($vetid);
           $vetEmail = $user->username;
           $vetCell = $user->cellPhone;
           $post['TextTo'] = $vetCell;
           $post['vetEmail'] = $vetEmail;


           if ($starttime > $now){
               if ($hrs <= 24) {
                   /* send email notification to customer */
                   craft()->emailUser->sendEmail($post);
                   $log_message = 'Send  <= 24 hours email notification ' .$post['emailType'].' '. $entry->getContent()->appointmentIdNumber." at hours:".$hrs;
                   EmailUserPlugin::log($log_message, LogLevel::Info, true);

                   /* send email reminder to vet */
                   $postvet['vetEmail'] = $vetEmail;
                   $postvet['emailType'] = 'vetApptReminder';
                   $postvet['vetFirstName']=$post_newvet['vetFirstName'];
                   $postvet['appointmentStartTime']=$post_newvet['appointmentLongDate'];
                   $postvet['appointmentIdNumber']=$post_newvet['appointmentIdNumber'];
                   $postvet['customerFirstName'] = $customeruser->firstName;
                   $postvet['customerLastName'] = $customeruser->lastName;
                   $postvet['customerIdNumber']=$post_newvet['customerIdNumber'];
                   $postvet['customerStreetAddress1'] = $entry->getContent()->customerStreetAddress1;
                   $postvet['customerStreetAddress2'] = $entry->getContent()->customerStreetAddress2;
                   $postvet['customerCity'] = $entry->getContent()->customerCity;
                   $postvet['customerState'] = $entry->getContent()->customerstate;
                   $postvet['customerZipCode'] = $entry->getContent()->customerZipCode;

                   $postvet['StartTime'] = $post_newvet['StartTime'];
                   $postvet['vetID']=$post_newvet['vetID'];
                   craft()->emailUser->sendEmail($postvet);
                   $log_message = 'Send  <= 24 hours email reminder notification to vet' .$post['emailType'].' '. $entry->getContent()->appointmentIdNumber." at hours:".$hrs;
                   EmailUserPlugin::log($log_message, LogLevel::Info, true);

                   /* send vet 24 hours appt notification vet24hrsApptNotificationTxt */
                   craft()->twilioSms->sendSms($post);
                   $log_message = 'send sms to ' . $post['TextTo'] . ' type: ' . $post['textType']." at hours:".$hrs;
                   TwilioSmsPlugin::log($log_message, LogLevel::Info, true);
               }else{
                   $log_message = "No '<= 24 hours email' send for ".$entry->getContent()->appointmentIdNumber." at hours:".$hrs;
                   EmailUserPlugin::log($log_message, LogLevel::Info, true);
               }
           }
           // $this->returnJson(array('charged' => $charged));

       }catch(exception $e){
           DisplacedAppointmentsPlugin::log('rebook : '.$hourDiff->h. " ".$hourDiff->d*24 ." ".$e->getMessage(), LogLevel::Info, true);
       }


       
        die();
    }

    public function actionCancel(){
        $creditApplied = false;
        $entryid = craft()->request->getPost('entryid');
        $entry = craft()->entries->getEntryById($entryid);
        $start = craft()->request->getPost('start');

        /* get vet info */
        $vetid = craft()->request->getPost('vetid');
        $vetuser =  craft()->users->getUserById($vetid);

        /* get customer info */
        $custid = craft()->request->getPost('customerid');
        $customeruser = craft()->users->getUserById($custid);

        $entry = craft()->entries->getEntryById($entryid);
        $entry->setContentFromPost(['cancel_status' => "cancelled"]); 

        $apptid = $entry->appointmentIdNumber;
        //$appointmentStart = strtotime($start);
       // $diff = abs(floor((time() - strtotime($entry->getContent()->appointmentStartTime)) / 3600));

         $appointmentStart = new DateTime($entry->appointmentStartTime);
                $now = new DateTime("now");
                $tz =  new \DateTimeZone('America/New_York');
                $now = $now->setTimeZone($tz);
                $hourDiff = $appointmentStart->diff($now);
                $hrs = $hourDiff->format("%H");
                $hrs = $hrs + (($hourDiff->format("%d"))*24);


    //      if($hrs <= 12){
            /* Apply cancellation credit */

                    $canellationCreditMatrix = craft()->fields->getFieldByHandle("cancellation_credit");  
                    $block = new MatrixBlockModel();
                    $block->fieldId    = $canellationCreditMatrix->id; // Matrix field's ID
                    $block->ownerId    = $customeruser->id; // ID of entry the block should be added to
                    $canellationCreditMatrixBlocks = craft()->matrix->getBlockTypesByFieldId($canellationCreditMatrix->id);
                    foreach($canellationCreditMatrixBlocks as $key => $data){ 
                        if($data->handle === "cancellation_credit"){ 
                        $creditBlock = $data; 
                        break; 
                        }; 
                    };
                    $block->typeId     =  $creditBlock->id; // ID of block type
                    $block->getContent()->setAttributes(array(
                        'used_status' => "unused",
                        'real_id' => $entryid,
                        'appt_id' => $apptid
                    ));
                    $success = craft()->matrix->saveBlock($block);
                    $creditApplied = true;
       //             }

        craft()->entries->saveEntry($entry);

        /* send customer custApptCancelledByVet message*/
      
        $post['emailType'] = 'custApptCancelledByVet';
        $post['customerFirstName'] = $customeruser->firstName;
        $post['customeremail'] = $customeruser->email;
        $starttime = new DateTime($entry->appointmentStartTime);
        $post['appointmentStartTime'] = $starttime->format('l F d, Y').' at '.$starttime->format(' g:i a');
        $post['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
        $post['customerIdNumber'] = $entry->getContent()->customerIdNumber;
     
        craft()->emailUser->sendEmail($post);
        $post['TextTo'] = $entry->getContent()->cellPhone;
        $post['textType'] = 'custApptCancelledTxt';
        /* send text if txtupdates = 'updates' */

        if ($entry->getContent()->txtupdates == 'updates') {
            try{
                craft()->twilioSms->sendSms($post);
                $log_message = 'send sms to ' . $post['TextTo'] . ' type: ' . $post['textType'];
                TwilioSmsPlugin::log($log_message, LogLevel::Info, true);
            }catch(Exception $e){
                $log_message = 'Error in send sms';
                TwilioSmsPlugin::log($log_message, LogLevel::Info, true);
            }
        }

        /* send admin adminApptCancelledByAdmin message */
        $post_admin['emailType'] = 'adminApptCancelledByAdmin';
        $post_admin['appointmentIdNumber'] = $entry->appointmentIdNumber;
        $post_admin['customerName'] = $customeruser->firstName.' '.$customeruser->lastName;
        $post_admin['customeremail'] = $customeruser->email;
        $post_admin['customerIdNumber'] = $entry->customerIdNumber;
        craft()->emailUser->sendEmail($post_admin);
        $log_message = "Send admin adminApptCancelledByAdmin ".$post_admin['appointmentIdNumber'];
        EmailUserPlugin::log($log_message, LogLevel::Info, true);
    
          //craft()->request->redirect(craft()->getSiteUrl().'admin/charge/appointments/processed');
        die();
    }
  public function actionApplyCredit(){
    $canellationCreditMatrix = craft()->fields->getFieldByHandle("cancellation_credit");  
    $userid = craft()->request->getPost('userid');
    $appointmentId = craft()->request->getPost('apid');
    $blockid = craft()->request->getPost('blockid');
    $userModel = craft()->users->getUserById($userid);
    $criteria = craft()->elements->getCriteria(ElementType::MatrixBlock);
    $criteria->fieldId = $canellationCreditMatrix->id;
    $criteria->ownerId = $userModel->id; // This is your Global Set id
    $matrixBlocks = $criteria->find();

    foreach($matrixBlocks as $block){
        if ($block->used_status == "unused" && $block->id == $blockid) {
            $block->getContent()->setAttributes(array(
                'used_status' => "used",
                'used_on' => $appointmentId
        ));
        $success = craft()->matrix->saveBlock($block);
        } 
    }
     $this->returnJson(array('results' => "done", 'rsp_type' => 1)); 
    }

    public function actionCustomerCancel(){
        $charged = false;
        $entryid = craft()->request->getPost('entryid'); 
        $start = craft()->request->getPost('start');

      //  $appointmentStart = strtotime($start);
     //   $diff = abs(floor((time() - $appointmentStart) / 3600));

        $entry = craft()->entries->getEntryById($entryid);
	    $vetid = substr($entry->assignedVeterinarianIdNumber, 3);
        $vetuser =  craft()->users->getUserById($vetid);
        
         $appointmentStart = new DateTime($entry->appointmentStartTime);
                $now = new DateTime("now");
                $tz =  new \DateTimeZone('America/New_York');
                $now = $now->setTimeZone($tz);
                $hourDiff = $appointmentStart->diff($now);
                $hrs = $hourDiff->format("%H");
                $hrs = $hrs + (($hourDiff->format("%d"))*24);

        if($hrs <= 24){
            $entry->setContentFromPost(['cancel_penalty' => "true"]); 
            $charged = true;
	       }

        $entry->setContentFromPost(['cancel_status' => "customer_cancelled"]); 
        craft()->entries->saveEntry($entry);
         $hold_criteria = craft()->elements->getCriteria(ElementType::Entry);
          $hold_criteria->section = 'holdappointment';
          $hold_criteria->timeslot = $entry->appointmentStartTime;
          $hold_criteria->vetemail = $vetuser->email;
          $hold_criteria->limit = 1;
          $hold_entries = $hold_criteria->find();
          if($hold_entries){
          $hold_entry = craft()->entries->getEntryById($hold_entries[0]->id);
          craft()->entries->deleteEntry($hold_entry);
        }
        $vetname = explode(" ",$entry->getContent()->assignedVeterinarianName);
       	$first = $vetname[0];
        $last = $vetname[1];

      	$vetEmail = $vetuser->email;
      	$customer_realid = $entry->getContent()->customerRealId;
      	$customer = craft()->users->getUserById($customer_realid);

        $customerFirst = $customer->firstName;
        $customerLast = $customer->lastName;

        $start_datetime = new DateTime($start);
      	        $post['appointmentStartTime'] = $start_datetime->format('l F d, Y').' at '.$start_datetime->format(' g:i a');
                $post['customerFirstName']= $customerFirst;
                $post['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
                $post['customerIdNumber'] = $entry->getContent()->customerIdNumber;
                $post['emailType'] = "custApptSelfCancelled";
                $post['customeremail'] = $entry->getContent()->customeremail;
     
	   craft()->emailUser->sendEmail($post);
        $log_message = "Send customer custApptSelfCancelled ".$post['appointmentIdNumber'];
        EmailUserPlugin::log($log_message, LogLevel::Info, true);
	
       /*
        * Admin email seems to be failing, might be problem with this line in EmailUserService " $element->group = 'adminWNotifications' "
	      */


                $post_admin['vetFirstName']= $first;
                $post_admin['vetLastName']= $last;
                 $post_admin['customerFirstName']= $customerFirst;
                 $post_admin['customerLastName']= $customerLast;
                 $post_admin['appointmentIdNumber']= $entry->getContent()->appointmentIdNumber;
                 $post_admin['customerIdNumber']= $entry->getContent()->customerIdNumber;
                 $post_admin['vetID']= $entry->getContent()->assignedVeterinarianIdNumber;
                 if($charged){
                 $post_admin['emailType']= "adminClientCancelledLessThan24Hrs";
               } else {
                $post_admin['emailType']= "adminClientCancelledGreaterThan24Hrs";
               }
	
        craft()->emailUser->sendEmail($post_admin);
        $log_message = "Send admin ".$post_admin['emailType'].' '.$post_admin['appointmentIdNumber'];
        EmailUserPlugin::log($log_message, LogLevel::Info, true);


                $post_vet['vetFirstName']= $first;
                $post_vet['vetLastName']= $last;
                $post_vet['appointmentStartTime']= $start_datetime->format('l F d, Y').' at '.$start_datetime->format(' g:i a');
                $post_vet['appointmentIdNumber']= $entry->getContent()->appointmentIdNumber;
                $post_vet['customerIdNumber']= $entry->getContent()->customerIdNumber;
                $post_vet['vetID']= $entry->getContent()->assignedVeterinarianIdNumber;
                $post_vet['customerFirstName']=$customerFirst;
                $post_vet['customerLastName']=$customerLast;
                $post_vet['customerStreetAddress1']=$entry->getContent()->customerStreetAddress1;
                $post_vet['customerStreetAddress2'] = $entry->getContent()->customerStreetAddress2;
                $post_vet['customerCity'] = $entry->getContent()->customerCity;
                $post_vet['customerState'] = $entry->getContent()->customerstate;
                $post_vet['customerZipCode'] = $entry->getContent()->customerZipCode;
                $post_vet['emailType']= "vetApptCancellationByCust";
                $post_vet['vetEmail']= $vetEmail;

        craft()->emailUser->sendEmail($post_vet);
        $log_message = "Send vet ".$post_vet['emailType'].' '.$post_vet['appointmentIdNumber'];
        EmailUserPlugin::log($log_message, LogLevel::Info, true);

        $this->returnJson(array('charged' => $charged));
        die();
    }

      public function actionVetCancelLessThan(){
        $creditApplied = false;
        $entryid = craft()->request->getPost('entryid');
        $entry = craft()->entries->getEntryById($entryid);
        $start = craft()->request->getPost('displaystart');
        $vetid = substr(craft()->request->getPost('vetid'), 3);
        $whole_vetid = craft()->request->getPost('vetid');
        $vetuser =  craft()->users->getUserById($vetid);
        $custid = $entry->customerRealId;
        $customeruser = craft()->users->getUserById($custid);
        $apptid = $entry->appointmentIdNumber;

         $appointmentStart = new DateTime($entry->appointmentStartTime);
                $now = new DateTime("now");
                $tz =  new \DateTimeZone('America/New_York');
                $now = $now->setTimeZone($tz);
                $hourDiff = $appointmentStart->diff($now);
                $hrs = $hourDiff->format("%H");
                $hrs = $hrs + (($hourDiff->format("%d"))*24);

         if  ($hrs <=12){
             /* when vet cancel in less than and equal to  12 hours, turn that to system cancel */
             $entry->setContentFromPost(['cancel_status' => "sys_cancelled"]);
         } else{
             /* when vet cancel in greater than 12 hours, turn that to admin cancel */
             $entry->setContentFromPost(['cancel_status' => "cancelled"]);
         }
          $entry->setContentFromPost(['cancel_penalty' => "true"]);

          /* Apply cancellation credit */

                    $canellationCreditMatrix = craft()->fields->getFieldByHandle("cancellation_credit");  
                    $block = new MatrixBlockModel();
                    $block->fieldId    = $canellationCreditMatrix->id; // Matrix field's ID
                    $block->ownerId    = $customeruser->id; // ID of entry the block should be added to
                    $canellationCreditMatrixBlocks = craft()->matrix->getBlockTypesByFieldId($canellationCreditMatrix->id);
                    foreach($canellationCreditMatrixBlocks as $key => $data){ 
                        if($data->handle === "cancellation_credit"){ 
                        $creditBlock = $data; 
                        break; 
                        }; 
                    };
                    $block->typeId     =  $creditBlock->id; // ID of block type
                    $block->getContent()->setAttributes(array(
                        'used_status' => "unused",
                        'real_id' => $entryid,
                        'appt_id' => $apptid
                    ));
                    $success = craft()->matrix->saveBlock($block);
                    $creditApplied = true;


        craft()->entries->saveEntry($entry);

        /* send customer custApptCancelledByVet message*/
      
        $post['emailType'] = 'custApptCancelledByVet';
        $post['customerFirstName'] = $customeruser->firstName;
        $post['customeremail'] = $customeruser->email;
        $starttime = new DateTime($entry->appointmentStartTime);
        $post['appointmentStartTime'] = $starttime->format('l F d, Y').' at '.$starttime->format(' g:i a');
        $post['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
        $post['customerIdNumber'] = $entry->getContent()->customerIdNumber;
     
        craft()->emailUser->sendEmail($post);
        $post['TextTo'] = $entry->getContent()->cellPhone;
        $post['textType'] = 'custApptCancelledTxt';
        /* send text if txtupdates = 'updates' */

        if ($entry->getContent()->txtupdates == 'updates') {
            try{
                craft()->twilioSms->sendSms($post);
                $log_message = 'send sms to ' . $post['TextTo'] . ' type: ' . $post['textType'];
                TwilioSmsPlugin::log($log_message, LogLevel::Info, true);
            }catch(Exception $e){
              $log_message = 'Error in send sms';
              TwilioSmsPlugin::log($log_message, LogLevel::Info, true);
          }
        }

        /* Send vet  vetApptCancellationByVet */
          $post_vet['vetFirstName']= $vetuser->firstName;
          $post_vet['appointmentIdNumber']= $entry->getContent()->appointmentIdNumber;
          $post_vet['appointmentDate']= $start;
          $post_vet['vetID']= $whole_vetid;
          $appointmentStart = new DateTime($entry->appointmentStartTime);
          $post_vet['appointmentStartTime'] = $appointmentStart->format('l F d, Y').' at '.$appointmentStart->format(' g:i a');
          $post_vet['emailType']= "vetApptCancellationByVet";
          $post_vet['vetEmail']= $vetuser->email;

          craft()->emailUser->sendEmail($post_vet);
          $log_message = "Send vet  ".$post_vet['emailType']." ".$post_vet['appointmentIdNumber'];
          EmailUserPlugin::log($log_message, LogLevel::Info, true);

          /* Send admin adminVetCancelledLessThan48hrs */
          if ($hrs < 48){
              $post_admin['vetFirstName']= $vetuser->firstName;
              $post_admin['vetLastName']= $vetuser->lastName;
              $post_admin['customerFirstName'] = $customeruser->firstName;
              $post_admin['customerLastName'] = $customeruser->lastName;
              $post_admin['customerIdNumber']= $entry->getContent()->customerIdNumber;
              $post_admin['appointmentIdNumber']= $entry->getContent()->appointmentIdNumber;
              $post_admin['vetID']= $whole_vetid;
              $post_admin['emailType']= "adminVetCancelledLessThan48hrs";
              craft()->emailUser->sendEmail($post_admin);
              $log_message = "Send Admin  ".$post_admin['emailType']." ".$post_admin['appointmentIdNumber'];
              EmailUserPlugin::log($log_message, LogLevel::Info, true);
          }


          /* send admin adminApptCancelledBySys message */
        $post_admin['emailType'] = 'adminApptCancelledBySys';
        $post_admin['appointmentIdNumber'] = $entry->appointmentIdNumber;
        $post_admin['customerName'] = $customeruser->firstName.' '.$customeruser->lastName;
        $post_admin['customeremail'] = $customeruser->email;
        $post_admin['customerIdNumber'] = $entry->customerIdNumber;
        craft()->emailUser->sendEmail($post_admin);
        $log_message = "Send admin adminApptCancelledBySys ".$post_admin['appointmentIdNumber'];
        EmailUserPlugin::log($log_message, LogLevel::Info, true);
         
       // $this->returnJson(array('results' => $creditApplied));
        die();
    }

    public function actionVetcancelled(){
         $this->renderTemplate('displacedappointments/vetter_cancelled');
    }
}
