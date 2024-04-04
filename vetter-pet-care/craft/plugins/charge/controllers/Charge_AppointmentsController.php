<?php
namespace Craft;

class Charge_AppointmentsController extends Charge_BaseCpController
{
    public function actionIndex(array $variables = [])
    {
        $this->renderTemplate('charge/appointments/index', $variables);
    }

     public function actionView(array $variables = [])
    {
       
        $variables['test'] = "test";
        $variables['mode'] = craft()->charge->getMode();

        $this->renderTemplate('charge/appointments/_view', $variables);
    }
    public function actionProcess(array $variables = []){

        $apptId = $variables['apptId'];
        if($this->validId($apptId)){
        $row = $this->getStripeId($apptId);
        $stripeId = $row['stripeId'];
        $customerId = $row['id'];
        $variables['customer'] = craft()->charge_customer->findById($customerId);
        $customer = $this->getStripeCustomerObj($stripeId);
        $variables['formattedCard'] = "**** **** **** ".$customer['sources']['data'][0]['last4'];
         $variables['exp_month'] = $customer['sources']['data'][0]['exp_month'];
         $variables['exp_year'] = $customer['sources']['data'][0]['exp_year'];
         $variables['stripeId'] = $stripeId;  
        $variables['entry'] = craft()->entries->getEntryById($apptId);
        $this->renderTemplate('charge/appointments/process', $variables);
        } else {
            craft()->request->redirect(craft()->getSiteUrl().'admin/charge/appointments');
        }
    }
    public function actionProcessed(array $variables = []){
         $this->renderTemplate('charge/appointments/processed', $variables);
    }
     public function actionViewProcessed(array $variables = []){

        $apptId = $variables['apptId'];
        if($this->validId($apptId)){
         $variables['id'] = $apptId;
         $variables['entry'] = craft()->entries->getEntryById($apptId);
        $this->renderTemplate('charge/appointments/viewProcessed', $variables);
        } else {
            craft()->request->redirect(craft()->getSiteUrl().'admin/charge/appointments/processed');
        }
    }

    public function actionEntrySubmit(array $variables = []){

     $amnt = craft()->request->getPost('amnt');
     $customer_id = craft()->request->getPost('customer_id');
        //$amount = $amnt."00";
        //$amount = (int)trim($amount);
      $amount=(int)(trim($amnt)*100);  
      if($amnt && $amount && $customer_id){
        if(craft()->charge->getMode() == "test"){
        \Stripe\Stripe::setApiKey("sk_test_ITW1oinyy47HUd9bTpxRBiQs");
        } else {
        \Stripe\Stripe::setApiKey("sk_live_4MeZadcmdkyIhkW2IpZhd0Ny");
        }
        $chrg = \Stripe\Charge::create(array(
        "amount" => $amount, // $15.00 this time
        "currency" => "usd",
        "customer" => $customer_id
            ));
            
        }
        // clear credit_error session after save new card//
        $_SESSION["credit_error"] = '';

            $chargeID = $chrg->id;
            $entryid = craft()->request->getPost('entry_id');   
            $entry = craft()->entries->getEntryById($entryid);

            $entry->setContentFromPost(['processedstatus' => 'Processed', 'amount' => $amnt, 'paymentid' => $chargeID]); 
        
            craft()->entries->saveEntry($entry);
        
      craft()->request->redirect(craft()->getSiteUrl().'admin/charge/appointments/processed');
    }
    public function validId($id){
       $entry = craft()->entries->getEntryById($id);
       if($entry){
        return true;
       } else {
        return false;
       }
    }
    public function getStripeCustomerObj($stripeId){
        if(craft()->charge->getMode() == "test"){
        \Stripe\Stripe::setApiKey("sk_test_ITW1oinyy47HUd9bTpxRBiQs");
        } else {
        \Stripe\Stripe::setApiKey("sk_live_4MeZadcmdkyIhkW2IpZhd0Ny");
        }
	$customer = \Stripe\Customer::retrieve($stripeId);

        return $customer;
    }
    public function actionRefundPayment()
    {
        $this->requirePostRequest();
        $this->requireElevatedSession();
        $entryid = craft()->request->getPost('entry_id');
        $paymentId = craft()->request->getRequiredPost('paymentId');
    //  $payment = craft()->charge_payment->getPaymentById($paymentId);
  if(craft()->charge->getMode() == "test"){
        \Stripe\Stripe::setApiKey("sk_test_ITW1oinyy47HUd9bTpxRBiQs");
        } else {
        \Stripe\Stripe::setApiKey("sk_live_4MeZadcmdkyIhkW2IpZhd0Ny");
        }
        \Stripe\Refund::create(array(
          "charge" => $paymentId,
        ));
        if ($entryid) {
            $entry = craft()->entries->getEntryById($entryid);
                    $entry->setContentFromPost(['processedstatus' => 'Refunded']); 
                    craft()->entries->saveEntry($entry);
            if (craft()->request->isAjaxRequest()) {
                $this->returnJson(['success' => true]);       
            } else {
                craft()->userSession->setNotice(Craft::t('Payment refunded.'));
               // $this->redirectToPostedUrl($payment);
            }
        } else {
            if (craft()->request->isAjaxRequest()) {
                $this->returnJson(['success' => false]);
            } else {
                craft()->userSession->setError(Craft::t('Couldn’t refund payment.'));
              //  craft()->urlManager->setRouteVariables(['payment' => $payment]);
            }
        }
    }
    public function getStripeId($id){
        $entry = craft()->entries->getEntryById($id);
        $uid = $entry->getContent()->customerIdNumber;
        $custid = explode('-',$uid);
        $customeruser = craft()->users->getUserById($custid[2]);
        $query = craft()->db->createCommand()->select('stripeId, id');
        $query->from('charge_customers');
        $query->where(array(
                'userId' =>  $customeruser->id,
                'mode' => craft()->charge->getMode()
            ));
        return $query->queryRow();
    }

    public function getStripeIdByUserID($user_id){
        $customeruser = craft()->users->getUserById($user_id);
        $query = craft()->db->createCommand()->select('stripeId, id');
        $query->from('charge_customers');
        $query->where(array(
            'userId' =>  $customeruser->id,
            'mode' => craft()->charge->getMode()
        ));
        return $query->queryRow();
    }


    public function actionConfirmStripeEmail()
    {
        $customer_email = craft()->request->getPost('customer_email');

        /* Get customer ID */
        $customer= craft()->users->getUserByEmail($customer_email);
        if ($customer){
            $variables['stripeInfo'] = $this->getStripeIdByUserID($customer['id']);
        }else{
            $variables['stripeInfo']['stripeId'] = '-1'; /* -1 is notFound */
        }

        $variables['customer_email'] = $customer_email;

//        $this->renderTemplate('charge/oneoffbilling/viewOneOffBilling', $variables);
        $this->renderTemplate('charge/oneoffbilling/viewStripeEmail', $variables);

    }
    public function actionOneOffBillingCharge()
    {
        $amnt = craft()->request->getPost('amnt');
        $customer_id = craft()->request->getPost('customer_id');
        //$amount = $amnt."00";
        //$amount = (int)trim($amount);
        $amount=(int)(trim($amnt)*100);
        if($amnt && $amount && $customer_id){
            if(craft()->charge->getMode() == "test"){
                \Stripe\Stripe::setApiKey("sk_test_ITW1oinyy47HUd9bTpxRBiQs");
            } else {
                \Stripe\Stripe::setApiKey("sk_live_4MeZadcmdkyIhkW2IpZhd0Ny");
            }
            $chrg = \Stripe\Charge::create(array(
                "amount" => $amount, // $15.00 this time
                "currency" => "usd",
                "customer" => $customer_id
            ));

        }

        $chargeID = $chrg->id;
        $variables['chargeID'] = $chargeID;
        $variables['charge_status'] = $chrg->status;
        $this->renderTemplate('charge/oneoffbilling/viewOneOffBillingProcess', $variables);

    }


}
