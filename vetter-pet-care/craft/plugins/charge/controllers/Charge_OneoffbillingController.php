<?php
namespace Craft;

class Charge_OneoffbillingController extends Charge_BaseCpController
{

    public function actionIndex(array $variables = [])
    {
        $this->renderTemplate('charge/oneoffbilling/index', $variables);
    }


}