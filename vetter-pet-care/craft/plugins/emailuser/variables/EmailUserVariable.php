<?php
namespace Craft;
/**
 * Created by PhpStorm.
 * User: achan90
 * Date: 5/27/17
 * Time: 10:30 PM
 */
class EmailUserVariable
{

    public function sendEmail($post)
    {
        return craft()->emailUser->sendEmail($post);
    }

    public function reminder($post)
    {
        return craft()->emailUser->reminder($post);
    }

    public function validateHoldAppt($post)
    {
        return craft()->emailUser->validateHoldAppt($post);
    }

}