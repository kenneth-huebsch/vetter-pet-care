<?php
/**
 * Created by PhpStorm.
 * User: angelachan
 * Date: 8/10/17
 * Time: 11:15 AM
 */

namespace Craft;
use Stripe\Error\Card;



class ApptIdService extends BaseApplicationComponent
{

    public function getid($customerid,$requesttime)
    {
       $apptIdRecord = ApptIdRecord::model()->findByAttributes(array('customerid'=>$customerid,'requesttime'=>$requesttime));

        if ($apptIdRecord)
        {
            echo trim($apptIdRecord->getAttribute('apptid'));
        }
        //achan 12-13-2018 add log
        $now = new DateTime();
        $log_message = "Get appt record ".trim($apptIdRecord->getAttribute('apptid'))." request date time: ".$now->format('Y-m-d H:i:s');
        ApptIdPlugin::log($log_message, LogLevel::Info, true);


    }

    public function createid($customerid,$requesttime)
    {
        echo $customerid;

            $now = new DateTime();
            $appIdRecord = new ApptIdRecord();
            $appIdRecord ->customerid = $customerid;
            $appIdRecord ->requesttime = $requesttime;
            $appIdRecord ->dateCreated = $now->format('Y-m-d H:i:s');
            $appIdRecord ->dateUpdated = $now->format('Y-m-d H:i:s');

            $appIdRecord -> save();

        //achan 12-13-2018  add log
        $log_message = "created appt for ".$customerid." request time: ".$requesttime." Date created: ".$now->format('Y-m-d H:i:s');
        ApptIdPlugin::log($log_message, LogLevel::Info, true);
        // ApptIdRecord::model()->save($appIdRecord);


        // ApptIdRecord::model()->save($appIdRecord);

    }

    public function verifycard($customerid,$charge_amount, $description)
    {
        if($charge_amount == 100){
            if(craft()->charge->getMode() == "test"){
                \Stripe\Stripe::setApiKey("sk_test_ITW1oinyy47HUd9bTpxRBiQs");
            } else {
                \Stripe\Stripe::setApiKey("sk_live_4MeZadcmdkyIhkW2IpZhd0Ny");
            }
            try {
                $chrg = \Stripe\Charge::create(array(
                    "amount" => $charge_amount, // $15.00 this time
                    "currency" => "usd",
                    "description" => $description,
                    "customer" => $customerid
                ));

                $chargeID = $chrg->id;

                if (!$chargeID){
                    echo "failed";
                    // $_SESSION["credit_error"] = $chrg->failure_message;
                }else{
                    // refund $1.00 back
                    \Stripe\Refund::create(array(
                        "charge" => $chrg->id,
                        "amount" => $charge_amount,
                    ));
                    echo "success";
                }

            }  catch(\Stripe\Error\Card $e) {
        // Since it's a decline, \Stripe\Error\Card will be caught
        $body = $e->getJsonBody();
        $err  = $body['error'];

                $_SESSION["credit_error"] = $err['message'];
//
//        print('Status is:' . $e->getHttpStatus() . "\n");
//        print('Type is:' . $err['type'] . "\n");
//        print('Code is:' . $err['code'] . "\n");
//
//        // param is '' in this case
//        print('Param is:' . $err['param'] . "\n");
//        print('Message is:' . $err['message'] . "\n");
    } catch (\Stripe\Error\InvalidRequest $e) {
        // Invalid parameters were supplied to Stripe's API
    } catch (\Stripe\Error\Authentication $e) {
        // Authentication with Stripe's API failed
        // (maybe you changed API keys recently)
    } catch (\Stripe\Error\ApiConnection $e) {
        // Network communication with Stripe failed
    } catch (\Stripe\Error\Base $e) {
        // Display a very generic error to the user, and maybe send
        // yourself an email
    } catch (Exception $e) {
        // Something else happened, completely unrelated to Stripe
    }






        }
    }

}