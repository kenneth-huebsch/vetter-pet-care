<?php
/**
 * GoogleApi_Calendar Controller
 *
 * @author    Kenneth Huebsch
 * @link      https://puffin.dev
 * @package   GoogleApi
 * @since     1.0.0
 */

namespace Craft;

class GoogleApi_CalendarController extends BaseController
{
    /**
     * Handle a request going to our plugin's index action URL, e.g.: actions/googleApi
     */
    public function actionCreateCalendarEvent()
    {
        $log_message = 'Action - Create Calendar Event';
        GoogleApiPlugin::log($log_message, LogLevel::Info, true);

        //get required info from post        
        $firstName = craft()->request->getRequiredPost('customerFirstName');
        $lastName = craft()->request->getRequiredPost('customerLastName');
        $customerCell = craft()->request->getRequiredPost('customerCell');
        $customerEmail = craft()->request->getRequiredPost('customerEmail');
        $streetAddress = craft()->request->getRequiredPost('streetAddress');
        $streetAddress2 = craft()->request->getRequiredPost('streetAddress2');
        $city = craft()->request->getRequiredPost('city');
        $customerZip = craft()->request->getRequiredPost('customerZip');
        $startTime = craft()->request->getRequiredPost('start');
        $endTime = craft()->request->getRequiredPost('end');
        $apptID = craft()->request->getRequiredPost('apptID');
        $vetFirstName = craft()->request->getRequiredPost('vetFirstName');
        $vetLastName = craft()->request->getRequiredPost('vetLastName');
        $petList = craft()->request->getRequiredPost('petList');

        //send required info to service
        craft()->googleApi_calendar->createCalendarEvent($firstName,
                                                         $lastName,
                                                         $customerCell,
                                                         $customerEmail,
                                                         $streetAddress,
                                                         $streetAddress2,
                                                         $city,
                                                         $customerZip,
                                                         $startTime,
                                                         $endTime,
                                                         $apptID,
                                                         $vetFirstName,
                                                         $vetLastName,
                                                         $petList);

        /* to avoid jquery 404 error */
        die();
    }
}