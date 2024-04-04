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

class DiscountsController extends BaseController
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = true;

    public function actionCheckDiscount(){
      $userid = craft()->request->getPost('userid');
      $discount_code = craft()->request->getPost('discount_code');
      $criteria = craft()->elements->getCriteria(ElementType::Entry);
      $criteria->section = 'discountCodes';
      $criteria->search = 'codeNumber:'.$discount_code;
      $find = $criteria->find();
      /* Get global message */
        $criteria = craft()->elements->getCriteria(ElementType::GlobalSet);
      // Set the handle you want
        $criteria->handle = 'booking';
      // Find the settings set
        $booking = $criteria->first();

      if(!$find){
        $find = "Invalid Discount Code";
        $this->returnJson(array('rsp_type' => 0, 'results' => $find));
      } else {
        $type = $find[0]['discount_type']->value;
        $value = $find[0]['value'];
        $expiring_on = $find[0]['expirationDate'];
        // Load the user
        $userModel = craft()->users->getUserById($userid);

        // Load the matrix field
        $couponsMatrix = craft()->fields->getFieldByHandle("coupons_used");


        //check for existing

            $criteria = craft()->elements->getCriteria(ElementType::MatrixBlock);
            $criteria->fieldId = $couponsMatrix->id;
            $criteria->ownerId = $userModel->id; // This is your Global Set id
            $c_matrixBlocks = $criteria->find();
            $exists = false;
            foreach($c_matrixBlocks as $c_block){
                if (strtolower($c_block->coupon_code) == strtolower($discount_code) && $c_block->used_status == "unused") {
                  $exists = true;
                  $msg = $booking->dcRejectionPerCustomer;
                   $this->returnJson(array('rsp_type' => 0,'results' => $msg)); 
                   break;
                } 
                if (strtolower($c_block->coupon_code) == strtolower($discount_code) && $c_block->used_status == "used") {
                  $exists = true;
                  $msg = $booking->dcRejectionPerCustomer;
                   $this->returnJson(array('rsp_type' => 0, 'results' => $msg)); 
                   break;
                }
            }

       //check for new customer
          if ($type == 'newCustomer'){
              $criteria = craft()->elements->getCriteria(ElementType::Entry);
              $criteria->section = 'appointmentRecords';
              $criteria->search = 'customerRealId:'.$userid;
              $findNewCustomer = $criteria->find();
              if ($findNewCustomer){
                  $msg = $booking->dcRejectionNewCustomer;
                  $exists = true;
                    $this->returnJson(array('rsp_type' => 0, 'results' => $msg));
              }else{
                  $exists = false;
              }
          }
              
if(!$exists){

        $block = new MatrixBlockModel();
        $block->fieldId    = $couponsMatrix->id; // Matrix field's ID
        $block->ownerId    = $userModel->id; // ID of entry the block should be added to
        $couponMatrixBlocks = craft()->matrix->getBlockTypesByFieldId($couponsMatrix->id);
        foreach($couponMatrixBlocks as $key => $data){ 
            if($data->handle === "coupons_used"){ 
            $couponsBlock = $data; 
            break; 
            }; 
        };
        $block->typeId     =  $couponsBlock->id; // ID of block type
        $block->getContent()->setAttributes(array(
            'coupon_code' => $discount_code,
            'discount_type' => $type,
            'value' => $value,
            'exp' => $expiring_on,
            'used_status' => "unused"
        ));
        $now = new DateTime('now');
        if ($expiring_on < $now){
            $find = $booking->dcRejectionExpiration;
            $rsp_type = 0;
        }else{
            $success = craft()->matrix->saveBlock($block);
            $find = $booking->dcSucessfulSubmission;
            $rsp_type = 1;
            }
        $this->returnJson(array('userid' => $userid, 'discount_code' => $discount_code, 'rsp_type' => $rsp_type, 'results' => $find));
          }
      }
    }
    public function actionUpdateCouponStatus(){
    $couponCodeMatrix = craft()->fields->getFieldByHandle("coupons_used");
    $userid = craft()->request->getPost('userid');
    $appointmentId = craft()->request->getPost('apid');
    $discount_code = craft()->request->getPost('discount_code');
    $userModel = craft()->users->getUserById($userid);
    $criteria = craft()->elements->getCriteria(ElementType::MatrixBlock);
    $criteria->fieldId = $couponCodeMatrix->id;
    $criteria->ownerId = $userModel->id; // This is your Global Set id
    $matrixBlocks = $criteria->find();

    foreach($matrixBlocks as $block){
        if (strtolower($block->coupon_code) == strtolower($discount_code) && $block->used_status == "unused") {
            $block->getContent()->setAttributes(array(
                'used_status' => "used",
                'ap_id' => $appointmentId
        ));
        $success = craft()->matrix->saveBlock($block);
        } 
    }
     $this->returnJson(array('results' => "done", 'rsp_type' => 1)); 
    }

    public function actionDeleteHold(){
        try{
            $entryId = craft()->request->getPost('entryId');
            $entry = craft()->entries->getEntryById($entryId);
            craft()->entries->deleteEntry($entry);
            if(craft()->httpSession->get('tab')){
                craft()->httpSession->remove('tab');
            }
            if(craft()->httpSession->get('flow')){
                craft()->httpSession->remove('flow');
            }
            if(craft()->httpSession->get('start')){
                craft()->httpSession->remove('start');
            }
            if(craft()->httpSession->get('end')){
                craft()->httpSession->remove('end');
            }
            if(craft()->httpSession->get('firstToSchedule')){
                craft()->httpSession->remove('firstToSchedule');
            }
            if(craft()->httpSession->get('petSchedule[]')){
                craft()->httpSession->remove('petSchedule[]');
            }
            //achan 12-13-2018 add log
            $log_message = "Delete hold".$entryId." from DeleteHold";
            DiscountsPlugin::log($log_message,LogLevel::Info,true);

            $this->returnJson(array('success' => "twig session ended"));
        }catch(exception $e){
            $log_message = "Delete hold".$entryId." from DeleteHold ".$e->getMessage();
            DiscountsPlugin::log($log_message,LogLevel::Info,true);
        }

    }
    public function autoDelete($id){
        try{
            $entryId = $id;
            $entry = craft()->entries->getEntryById($entryId);
            craft()->entries->deleteEntry($entry);
            //achan 12-13-2018 add log
            $log_message = "Delete ".$entryId." from autoDelete";
            DiscountsPlugin::log($log_message,LogLevel::Info,true);
        }catch (exception $e){
            $log_message = "Delete ".$entryId." from autoDelete ". $e->getMessage();
            DiscountsPlugin::log($log_message,LogLevel::Info,true);
        }

    }
    public function actionConfirm() {
        try{
            $entryId = craft()->request->getPost('holdid');
            $entry = craft()->entries->getEntryById($entryId);
            $entry->setContentFromPost(['hold_status' => "confirmed"]);
            craft()->entries->saveEntry($entry);

            //achan 12-13-2018 add log
            $log_message = "Hold id ".$entryId." confirmed - from actionConfirm";
            DiscountsPlugin::log($log_message,LogLevel::Info,true);

            $this->returnJson(array('success' => "confirmed"));
        }catch(exception $e){
            $log_message = "Hold id ".$entryId." ".$e->getMessage()." - from actionConfirm";
            DiscountsPlugin::log($log_message,LogLevel::Info,true);
        }


       
       die();
    }
    public function actionCheckHolds(){
        try{
            $hold_criteria = craft()->elements->getCriteria(ElementType::Entry);
            $hold_criteria->section = 'holdappointment';
            //$hold_criteria->hold_status = "pending";
            $hold_criteria->limit = null;
            $hold_entries = $hold_criteria->find();
            foreach($hold_entries as $hold){
                $diff = abs(floor((time() - $hold->time_created) / 60));
                if(($diff > 15 || empty($hold->hold_status)) && $hold->hold_status != "confirmed"){
                    $hold_id = $hold->id;
                    $this->autoDelete($hold->id);
                    $log_message = "Hold ".$hold_id." deleted - from actionCheckHolds";
                    DiscountsPlugin::log($log_message,LogLevel::Info,true);
                } elseif(time() > strtotime($hold->timeslot)){
                    $hold_id = $hold->id;
                    $this->autoDelete($hold->id);
                    $log_message = "Hold ".$hold_id." deleted - from actionCheckHolds";
                    DiscountsPlugin::log($log_message,LogLevel::Info,true);
                    // echo $hold->id." ".$hold->timeslot."<br>";
                }
            }
        }catch(exception $e){
            $log_message = $e->getMessage();
            DiscountsPlugin::log($log_message,LogLevel::Info,true);
        }

          die();
    }


/*
  
   public function actionEntrySubmit(){
    
    $this->requireAjaxRequest();
    $charged = false;
    $entryid = craft()->request->getPost('entryid'); 
    $status = craft()->request->getPost('status');  
    $start = craft()->request->getPost('start');
    $entry = craft()->entries->getEntryById($entryid);
    if($entry->getContent()->cancel_penalty != "true"){
        $appointmentStart = strtotime($start);
        $diff = abs(floor((time() - $appointmentStart) / 3600));

          if($diff <= 48){
            $entry->setContentFromPost(['cancel_penalty' => "true"]); 
            $charged = true;
             }
         }

    $entry->setContentFromPost(['cancel_status' => $status]); 

    craft()->entries->saveEntry($entry);
    $this->returnJson(array('charged' => $charged));
      //craft()->request->redirect(craft()->getSiteUrl().'admin/charge/appointments/processed');
      die();
    }
    public function actionRebook(){
        $this->requireAjaxRequest();
         $charged = false;
        $mode = craft()->request->getPost('mode'); 
        $entryid = craft()->request->getPost('entryid'); 
        $status = craft()->request->getPost('status'); 
        $vetname = craft()->request->getPost('vetname');
        $vetid = craft()->request->getPost('userid');
        $start = craft()->request->getPost('start');
        $criteria = craft()->elements->getCriteria(ElementType::User);
        $users = $criteria->search('id:'.$vetid);

        $formattedVetId = "v-0".$vetid;
        $newTitle = "Booking for ".$vetname;
        $entry = craft()->entries->getEntryById($entryid);

          if($entry->getContent()->cancel_penalty != "true"){
                $appointmentStart = strtotime($start);
                $diff = abs(floor((time() - $appointmentStart) / 3600));

                  if($diff <= 48){
                    $entry->setContentFromPost(['cancel_penalty' => "true"]); 
                    $charged = true;
                    }
             }
       if($mode == "auto"){
         $first = explode(" ",$entry->getContent()->assignedVeterinarianName)[0];
         $last = explode(" ",$entry->getContent()->assignedVeterinarianName)[1];
        $criteria = craft()->elements->getCriteria(ElementType::User);
        $criteria->search = 'firstName:'.$first;
        $user = $criteria->find();
        $vetEmail = $user[0]['username'];
             $post_vet['vetFirstName']= $first;
                $post_vet['appointmentIdNumber']= $entry->getContent()->appointmentIdNumber;
                $post_vet['emailType']= "vetApptCancellationByVet";
                $post_vet['vetEmail']= $vetEmail;

   //   craft()->emailUser->sendEmail($post_vet);
        }
        $oa = $entry->getContent()->original_author;
        if($oa){
          $original = $oa;
        } else {
          $original = $entry->getContent()->assignedVeterinarianName;
        }
       

       $authorsMatrix = craft()->fields->getFieldByHandle("authors");

        $block = new MatrixBlockModel();
        $block->fieldId    = $authorsMatrix->id; // Matrix field's ID
        $block->ownerId    = $userModel->id; // ID of entry the block should be added to
        $block->typeId     = 7; // ID of block type

        $block->getContent()->setAttributes(array(
            'streetAddress' => '1523 Kansas Ave'
        ));

        $success = craft()->matrix->saveBlock($block);
      
        $entry->getContent()->title = $newTitle;
        $entry->setContentFromPost(['title' => $newTitle, 
                                    'assignedVeterinarianIdNumber' => $formattedVetId,
                                    'assignedVeterinarianName' => $vetname,
                                    'cancel_status' => "rebooked",
                                    'original_author' => $original]); 

        craft()->entries->saveEntry($entry);
        $this->returnJson(array('charged' => $charged));
          //craft()->request->redirect(craft()->getSiteUrl().'admin/charge/appointments/processed');
        die();
    }

    public function actionCancel(){
        $entryid = craft()->request->getPost('entryid'); 
        $entry = craft()->entries->getEntryById($entryid);
        $entry->setContentFromPost(['cancel_status' => "cancelled"]); 
        craft()->entries->saveEntry($entry);
            
          //craft()->request->redirect(craft()->getSiteUrl().'admin/charge/appointments/processed');
        die();
    }

    public function actionCustomerCancel(){
        $charged = false;
        $entryid = craft()->request->getPost('entryid'); 
        $start = craft()->request->getPost('start');

        $appointmentStart = strtotime($start);
        $diff = abs(floor((time() - $appointmentStart) / 3600));

        $entry = craft()->entries->getEntryById($entryid);
	
        if($diff <= 24){
            $entry->setContentFromPost(['cancel_penalty' => "true"]); 
            $charged = true;
	       }

        $entry->setContentFromPost(['cancel_status' => "customer_cancelled"]); 
        craft()->entries->saveEntry($entry);

       	$first = explode(" ",$entry->getContent()->assignedVeterinarianName)[0];
        $last = explode(" ",$entry->getContent()->assignedVeterinarianName)[1];
        
	      $criteria = craft()->elements->getCriteria(ElementType::User);
      	$criteria->search = 'firstName:'.$first;
      	$user = $criteria->find();
      	$vetEmail = $user[0]['username'];

                $post['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
                $post['customerIdNumber'] = $entry->getContent()->customerIdNumber;
                $post['emailType'] = "custApptSelfCancelled";
                $post['customeremail'] = $entry->getContent()->customeremail;
     
	   craft()->emailUser->sendEmail($post);
	
       
         Admin email seems to be failing, might be problem with this line in EmailUserService " $element->group = 'adminWNotifications' "
	

	
                $post_admin['vetFirstName']= $first;
                $post_admin['vetLastName']= $last;
            	$customerFirst = explode(" ", $entry->getContent()->customerName)[0];
            	$customerLast = explode(" ", $entry->getContent()->customerName)[1];
                 $post_admin['customerFirstName']= $customerFirst;
                 $post_admin['customerLastName']= $customerLast;
                 $post_admin['appointmentIdNumber']= $entry->getContent()->appointmentIdNumber;
                 $post_admin['customerIdNumber']= $entry->getContent()->customerIdNumber;
                 if($charged){
                 $post_admin['emailType']= "adminClientCancelledLessThan24Hrs";
               } else {
                $post_admin['emailType']= "adminClientCancelledGreaterThan24Hrs";
               }
	
        craft()->emailUser->sendEmail($post_admin);

	

                $post_vet['vetFirstName']= $first;
                $post_vet['appointmentStartTime']= $entry->getContent()->appointmentStartTime;
                $post_vet['appointmentIdNumber']= $entry->getContent()->appointmentIdNumber;
                $post_vet['customerIdNumber']= $entry->getContent()->customerIdNumber;
                $post_vet['emailType']= "vetApptCancellationByCust";
                $post_vet['vetEmail']= $vetEmail;

        craft()->emailUser->sendEmail($post_vet);

        $this->returnJson(array('charged' => $charged));
        die();
    }

    public function actionVetcancelled(){
        // $this->renderTemplate('displacedappointments/vetter_cancelled');
    }
*/
    
}
