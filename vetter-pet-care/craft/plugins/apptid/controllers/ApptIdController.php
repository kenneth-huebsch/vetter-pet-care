<?php
/**
 * Created by PhpStorm.
 * User: angelachan
 * Date: 8/10/17
 * Time: 11:57 AM
 */

namespace Craft;


class ApptIdController extends BaseController{
    protected $allowAnonymous = true;

    public function actionCreateid()
    {
        $customerid = craft()->request->getRequiredParam('customerid');
        $requesttime = craft()->request->getRequiredParam('requesttime');

        craft()->apptId->createid($customerid,$requesttime);

        /* to avoid jquery 404 error */
        die();
    }

    public function actionGetid()
    {
        $customerid = craft()->request->getRequiredParam('customerid');
        $requesttime = craft()->request->getRequiredParam('requesttime');

        craft()->apptId->getid($customerid,$requesttime);

        /* to avoid jquery 404 error */
        die();

    }

    public function actionVerifycard()
    {
        $customerid = craft()->request->getRequiredParam('customer_id');
        $charge_amount = craft()->request->getRequiredParam('customer_amount');
        $description = craft()->request->getRequiredParam('description');

        craft()->apptId->verifycard($customerid,$charge_amount, $description);

        /* to avoid jquery 404 error */
        die();

    }


}