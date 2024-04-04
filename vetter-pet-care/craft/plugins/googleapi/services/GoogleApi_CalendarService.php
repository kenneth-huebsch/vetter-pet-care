<?php
/**
 * GoogleApi_Calendar Service
 *
 * @author    Kenneth Huebsch
 * @link      https://puffin.dev
 * @package   GoogleApi
 * @since     1.0.0
 */
namespace Craft;

class GoogleApi_CalendarService extends BaseApplicationComponent
{
    /**
     * Uses the google client library to create a calendar event.
     */
    public function createCalendarEvent($firstName, $lastName, $customerCell, $customerEmail, $streetAddress, $streetAddress2,
        $city, $customerZip, $startTime, $endTime, $apptID, $vetFirstName, $vetLastName, $petList)
    {
        $startDT = new DateTime($startTime);
        $summary = $startDT->format("g:ia").' '.$vetFirstName." Appt, ".$streetAddress.', '.$streetAddress2;
        
        $log_message = 'New request to create calendar event for '.$summary;
        GoogleApiPlugin::log($log_message, LogLevel::Info, true);

        //only create calendar event in production envrionment
        if(craft()->config->get('environmentVariables')['env'] == 'prod')
        {
            $creds = '/.google/google-calendar-prod-api-creds.json';
            $calendarId = 'vetterpetcare.com_2kmsa52r3j5mgm37v1k3fefmqs@group.calendar.google.com';

            $lib = craft()->path->getPluginsPath().'googleapi/library/google-api-php-client-2.4.1_PHP54/vendor/autoload.php';
            require_once $lib;

            //initialize a new google client
            $client = new \Google_Client();

            //set application credentials
            putenv('GOOGLE_APPLICATION_CREDENTIALS='.$creds);
            $client->useApplicationDefaultCredentials();

            //prepare client
            $client->setApplicationName("Vetter");
            $client->setScopes(\Google_Service_Calendar::CALENDAR);      
            $client->setAccessType('offline');

            //get the service via the client
            $service = new \Google_Service_Calendar($client);
     
            //create an event to send to the calendar
            $description = 'Time: '.$startDT->format("g:ia")."\n".
                        'Customer: '.$firstName.' '.$lastName."\n".
                        'Pets: '.$petList."\n".
                        'Address: '.$streetAddress.', '.$streetAddress2.', '.$city.' '.$customerZip."\n".
                        'Cell: '.$customerCell."\n".
                        'Email: '.$customerEmail."\n".
                        'Vet: '.$vetFirstName.' '.$vetLastName."\n".
                        'Apptointment ID: '.$apptID."\n".
                        'Charges: '.'https://forms.gle/iwxrnnLKbHGYxCa19';

            $event = new \Google_Service_Calendar_Event(array('summary' => $summary,
                                                            'description' => $description,
                                                            'start' => array('dateTime' => $startTime),
                                                            'end' => array('dateTime' => $endTime)
                                                            ));
            $event = $service->events->insert($calendarId, $event);

            $log_message = 'Created '.$summary.' calendar event successfully';
            GoogleApiPlugin::log($log_message, LogLevel::Info, true);
        }
    }

}