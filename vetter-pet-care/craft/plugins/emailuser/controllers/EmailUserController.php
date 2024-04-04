<?php
namespace Craft;

/**
 * EmailUserController
 */
class EmailUserController extends BaseController
{
    protected $allowAnonymous = true;

    public function actionSend_email()
    {
        $this->requireAjaxRequest();
        //$appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
        $post['emailType'] = craft()->request->getRequiredPost('emailType');

        switch ($post['emailType']) {
            /* Customer Message */
            case 'custApptConfirmed':
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                $post['appointmentLongDate'] = craft()->request->getRequiredPost('appointmentLongDate');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['StartTime'] = craft()->request->getRequiredPost('StartTime');
                $post['appointmentStartTime'] = craft()->request->getRequiredPost('appointmentStartTime');
                $post['appointmentEndTime'] = craft()->request->getRequiredPost('appointmentEndTime');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['customerStreetAddress1'] = craft()->request->getRequiredPost('customerStreetAddress1');
                $post['customerStreetAddress2'] = craft()->request->getRequiredPost('customerStreetAddress2');
                $post['customerCity'] = craft()->request->getRequiredPost('customerCity');
                $post['customerZipCode'] = craft()->request->getRequiredPost('customerZipCode');
                $post['customerState'] = craft()->request->getRequiredPost('customerState');
                $post['emailType'] = craft()->request->getRequiredPost('emailType');
                $post['customeremail'] = craft()->request->getRequiredPost('customeremail');
                $post['CustID'] = craft()->request->getRequiredPost('CustID');
                break;
            case 'custApptSelfCancelled':
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['emailType'] = craft()->request->getRequiredPost('emailType');
                $post['customeremail'] = craft()->request->getRequiredPost('customeremail');
                break;
            case 'vetApptNotification':
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                $post['StartTime'] = craft()->request->getRequiredPost('StartTime');
                $post['appointmentStartTime'] = craft()->request->getRequiredPost('appointmentStartTime');
                $post['appointmentLongDate'] = craft()->request->getRequiredPost('appointmentLongDate');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['customerLastName'] = craft()->request->getRequiredPost('customerLastName');
                $post['customerStreetAddress1'] = craft()->request->getRequiredPost('customerStreetAddress1');
                $post['customerStreetAddress2'] = craft()->request->getRequiredPost('customerStreetAddress2');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['customerCity'] = craft()->request->getRequiredPost('customerCity');
                $post['customerZipCode'] = craft()->request->getRequiredPost('customerZipCode');
                $post['customerState'] = craft()->request->getRequiredPost('customerState');
                $post['emailType'] = craft()->request->getRequiredPost('emailType');
                $post['vetID'] = craft()->request->getRequiredPost('vetID');
                $post['vetcellPhone'] = craft()->request->getRequiredPost('vetcellPhone');
                $post['vetEmail'] = craft()->request->getRequiredPost('vetEmail');
                $post['vetFirstName'] = craft()->request->getRequiredPost('vetFirstName');
                $post['appointmentLongDate'] = craft()->request->getRequiredPost('appointmentLongDate');
                $post['petlist'] = craft()->request->getRequiredPost('petlist');
                $log_message = $post['appointmentIdNumber'] . '  vetApptNotification. ' . $post['petlist'];
                EmailUserPlugin::log($log_message, LogLevel::Info, true);
                break;
                break;
            case 'vetApptCancellationByVet':
                $post['vetFirstName'] = craft()->request->getRequiredPost('vetFirstName');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['appointmentStartTime'] = craft()->request->getRequiredPost('appointmentStartTime');
                $post['emailType'] = craft()->request->getRequiredPost('emailType');
                $post['vetID'] = craft()->request->getRequiredPost('vetID');
                $post['vetEmail'] = craft()->request->getRequiredPost('vetEmail');
                break;
            case 'vetApptCancellationByCust':
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['vetFirstName'] = craft()->request->getRequiredPost('vetFirstName');
                $post['vetLastName'] = craft()->request->getRequiredPost('vetLastName');
                $post['appointmentStartTime'] = craft()->request->getRequiredPost('appointmentStartTime');
                $post['appointmentLongDate'] = craft()->request->getRequiredPost('appointmentLongDate');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerName'] = craft()->request->getRequiredPost('customerName');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['customerStreetAddress1'] = craft()->request->getRequiredPost('customerStreetAddress1');
                $post['customerStreetAddress2'] = craft()->request->getRequiredPost('customerStreetAddress2');
                $post['customerZipCode'] = craft()->request->getRequiredPost('customerZipCode');
                $post['customerCity'] = craft()->request->getRequiredPost('customerCity');
                $post['customerState'] = craft()->request->getRequiredPost('customerState');
                $post['vetID'] = craft()->request->getRequiredPost('vetID');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['customerLastName'] = craft()->request->getRequiredPost('customerLastName');
                $post['emailType'] = craft()->request->getRequiredPost('emailType');
                $post['vetEmail'] = craft()->request->getRequiredPost('vetEmail');
                break;

            case 'adminApptBookedByCust':
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                $post['StartTime'] = craft()->request->getRequiredPost('StartTime');
                $post['appointmentStartTime'] = craft()->request->getRequiredPost('appointmentStartTime');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['customerLastName'] = craft()->request->getRequiredPost('customerLastName');
                $post['customerStreetAddress1'] = craft()->request->getRequiredPost('customerStreetAddress1');
                $post['customerStreetAddress2'] = craft()->request->getRequiredPost('customerStreetAddress2');
                $post['customerCity'] = craft()->request->getRequiredPost('customerCity');
                $post['customerZipCode'] = craft()->request->getRequiredPost('customerZipCode');
                $post['customerState'] = craft()->request->getRequiredPost('customerState');
                $post['emailType'] = craft()->request->getRequiredPost('emailType');
                $post['vetID'] = craft()->request->getRequiredPost('vetID');
                $post['vetcellPhone'] = craft()->request->getRequiredPost('vetcellPhone');
                $post['vetEmail'] = craft()->request->getRequiredPost('vetEmail');
                $post['vetFirstName'] = craft()->request->getRequiredPost('vetFirstName');
                $post['vetLastName'] = craft()->request->getRequiredPost('vetLastName');
                $post['appointmentLongDate'] = craft()->request->getRequiredPost('appointmentLongDate');
                $post['petlist'] = craft()->request->getRequiredPost('petlist');
                $post['adminNotes'] = craft()->request->getRequiredPost('adminNotes');
                $log_message = $post['appointmentIdNumber'] . '  adminApptBookedByCust. ' . $post['petlist'];
                EmailUserPlugin::log($log_message, LogLevel::Info, true);
                break;

            case 'adminVetCancelledGreaterThan48hrs':
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                $post['vetFirstName'] = craft()->request->getRequiredPost('vetFirstName');
                $post['vetLastName'] = craft()->request->getRequiredPost('vetLastName');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['customerLastName'] = craft()->request->getRequiredPost('customerLastName');
                $post['customerStreetAddress1'] = craft()->request->getRequiredPost('customerStreetAddress1');
                $post['customerStreetAddress2'] = craft()->request->getRequiredPost('customerStreetAddress2');
                $post['customerCity'] = craft()->request->getRequiredPost('customerCity');
                $post['customerZipCode'] = craft()->request->getRequiredPost('customerZipCode');
                $post['customerState'] = craft()->request->getRequiredPost('customerState');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['vetID'] = craft()->request->getRequiredPost('vetID');
                $post['emailType'] = craft()->request->getRequiredPost('emailType');
                break;

            case 'adminVetCancelledLessThan48hrs':
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                $post['vetFirstName'] = craft()->request->getRequiredPost('vetFirstName');
                $post['vetLastName'] = craft()->request->getRequiredPost('vetLastName');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['customerLastName'] = craft()->request->getRequiredPost('customerLastName');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['vetID'] = craft()->request->getRequiredPost('vetID');
                $post['emailType'] = craft()->request->getRequiredPost('emailType');
                break;

            case 'adminClientCancelledLessThan24Hrs':
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                $post['vetFirstName'] = craft()->request->getRequiredPost('vetFirstName');
                $post['vetLastName'] = craft()->request->getRequiredPost('vetLastName');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['customerLastName'] = craft()->request->getRequiredPost('customerLastName');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['vetID'] = craft()->request->getRequiredPost('vetID');
                $post['emailType'] = craft()->request->getRequiredPost('emailType');
                break;

            case 'adminClientCancelledGreaterThan24Hrs':
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                $post['vetFirstName'] = craft()->request->getRequiredPost('vetFirstName');
                $post['vetLastName'] = craft()->request->getRequiredPost('vetLastName');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['customerLastName'] = craft()->request->getRequiredPost('customerLastName');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['vetID'] = craft()->request->getRequiredPost('vetID');
                $post['emailType'] = craft()->request->getRequiredPost('emailType');
                break;

            case 'vetApptReminder':
//                $starttime = new DateTime(craft()->request->getRequiredPost('appointmentStartTime'));
//                $now = new DateTime("now");
//                $tz =  new \DateTimeZone('America/New_York');
//                $now = $now->setTimeZone($tz);
//                $dtDiff = $starttime->diff($now);
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                $post['appointmentStartTime'] = craft()->request->getRequiredPost('appointmentStartTime');
                $post['appointmentLongDate'] = craft()->request->getRequiredPost('appointmentLongDate');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['StartTime'] = craft()->request->getRequiredPost('StartTime');
                $post['appointmentEndTime'] = craft()->request->getRequiredPost('appointmentEndTime');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['customerLastName'] = craft()->request->getRequiredPost('customerLastName');
                $post['customerStreetAddress1'] = craft()->request->getRequiredPost('customerStreetAddress1');
                $post['customerStreetAddress2'] = craft()->request->getRequiredPost('customerStreetAddress2');
                $post['customerCity'] = craft()->request->getRequiredPost('customerCity');
                $post['customerState'] = craft()->request->getRequiredPost('customerState');
                $post['customerZipCode'] = craft()->request->getRequiredPost('customerZipCode');
                $post['emailType'] = "vetApptReminder";
                $post['vetFirstName'] = craft()->request->getRequiredPost('vetFirstName');
                $post['petlist'] = craft()->request->getRequiredPost('petlist');
                /* for text message */
                $post['vetCell'] = craft()->request->getRequiredPost('vetcellPhone');
                $post['vetEmail'] = craft()->request->getRequiredPost('vetEmail');
                $post['vetID'] = craft()->request->getRequiredPost('vetID');
                $post['CustID'] = craft()->request->getRequiredPost('CustID');

                //craft()->emailUser->sendEmail($post_vet);
                $log_message = $post['appointmentIdNumber'] . '  vetApptReminder ' . $post['petlist'];
                //$log_message = $post['appointmentIdNumber'].' '.$post['StartTime'].' > '.$now.' vetApptReminder '.$starttime->format('Y-m-d H:i').' '.$now->format('Y-m-d H:i').' Day:'.$dtDiff->format("%d").' Hours:'.$dtDiff->format("%H");
                EmailUserPlugin::log($log_message, LogLevel::Info, true);

                break;

            case 'custApptReminder':
                // $starttime = new DateTime(craft()->request->getRequiredPost('appointmentStartTime'));
//                $now = new DateTime("now");
//                $tz =  new \DateTimeZone('America/New_York');
//                $now = $now->setTimeZone($tz);
//                $dtDiff = $starttime->diff($now);
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                $post['appointmentLongDate'] = craft()->request->getRequiredPost('appointmentLongDate');
                $post['appointmentStartTime'] = craft()->request->getRequiredPost('appointmentStartTime');
                $post['customerFirstName'] = craft()->request->getRequiredPost('customerFirstName');
                $post['StartTime'] = craft()->request->getRequiredPost('StartTime');
                $post['appointmentEndTime'] = craft()->request->getRequiredPost('appointmentEndTime');
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $post['customerIdNumber'] = craft()->request->getRequiredPost('customerIdNumber');
                $post['customerStreetAddress1'] = craft()->request->getRequiredPost('customerStreetAddress1');
                $post['customerStreetAddress2'] = craft()->request->getRequiredPost('customerStreetAddress2');
                $post['customerCity'] = craft()->request->getRequiredPost('customerCity');
                $post['customerState'] = craft()->request->getRequiredPost('customerState');
                $post['customerZipCode'] = craft()->request->getRequiredPost('customerZipCode');
                $post['emailType'] = "custApptReminder";
                $post['customeremail'] = craft()->request->getRequiredPost('customeremail');
                $post['vetName'] = craft()->request->getRequiredPost('vetFirstName') . ' ' . craft()->request->getRequiredPost('vetLastName');;
                $post['CustID'] = craft()->request->getRequiredPost('CustID');
                $log_message = 'Send customer 24 hour reminder ' . $post['appointmentIdNumber'] . ' ' . $post['appointmentStartTime'];
                EmailUserPlugin::log($log_message, LogLevel::Info, true);
                //craft()->emailUser->sendEmail($post);
                break;

            case 'adminNoHoldID':
                $post['emailType'] = "adminNoHoldID";
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                break;

            case 'adminHoldingApptMissing':
                $post['emailType'] = "adminHoldingApptMissing";
                $post['appointmentIdNumber'] = craft()->request->getRequiredPost('appointmentIdNumber');
                $appointmentDate = explode(' ', craft()->request->getRequiredPost('appointmentDate'));
                $post['appointmentDate'] = $appointmentDate[0] . ' ';
                break;

            default:
                $log_message = "No message is sent";
                $send = false;

        }

        craft()->emailUser->sendEmail($post);

        /* log */
        $log_message = "Call Send Email, type " . $post['emailType'];
        EmailUserPlugin::log($log_message, LogLevel::Info, true);

        /* to avoid jquery 404 error */
        die();
    }

    public function actionReminder()
    {
        /* call log*/
        $log_message = 'call Reminder function';
        EmailUserPlugin::log($log_message, LogLevel::Info, true);

        /* Only check the pass 31 days records */
        $queryStartDate = date('Y-m-d', strtotime("-31 days"));

        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->section = 'appointmentRecords';
        //$criteria->postDate = array('and', '>='.$queryStartDate, '<'.$queryEndDate);
        $criteria->limit = null;
        $criteria->postDate = '>=' . $queryStartDate;
        $entries = $criteria->find();

        if ($entries) {
            foreach ($entries as $entry) {
                $starttime = new DateTime($entry->getContent()->appointmentStartTime);
                $now = new DateTime("now");
                $tz = new \DateTimeZone('America/New_York');
                $now = $now->setTimeZone($tz);
                $dtDiff = $starttime->diff($now);
                $entrystatus = trim($entry->getContent()->cancel_status);

                /* achan 06-26-2019 get pets name */
                $matrixCriteria = craft()->elements->getCriteria(\Craft\ElementType::MatrixBlock);
                $matrixCriteria->type = 'pet';
                $matrixCriteria->ownerId = $entry->id;
                $matrixPets = $matrixCriteria->find();

                if ($matrixPets) {
                    $count = 0;
                    $send = false;
                    $pets_name = '';
                    foreach ($matrixPets as $matrixPet) {
                        if (!$matrixPet->deceased) {
                            if ($count == 0) {
                                $pets_name = $matrixPet->petFirstName;
                            } else {
                                $pets_name = $pets_name . ', ' . $matrixPet->petFirstName;
                            }
                            $count++;
                            $send = true; /* turn to true when any pet is not in deceased */
                        }
                    }
                }
                $post_vet['petlist'] = $pets_name;

                $log_message = 'check pet list: ' . $post_vet['petlist'];
                EmailUserPlugin::log($log_message, LogLevel::Info, true);


                if ($starttime > $now) {
                    if (($dtDiff->format("%d") == '1') && ($dtDiff->format("%H") == '0')) {
                        if (($entrystatus == 'rebooked') || ($entrystatus == '') || ($entrystatus == 'unavailable')) {
                            /* send 24 hours vet reminder*/
                            $log_message = 'Send vet 24 hour reminder ' . $entry->getContent()->appointmentIdNumber . ' ' . $starttime->format('Y-m-d H:i') . ' ' . $now->format('Y-m-d H:i') . ' Day:' . $dtDiff->format("%d") . ' Hours:' . $dtDiff->format("%H");
                            EmailUserPlugin::log($log_message, LogLevel::Info, true);
                            $post_vet['appointmentStartTime'] = $starttime->format('Y-m-d g:i a');
                            $post_vet['appointmentLongDate'] = $starttime->format('l, F d Y');
                            $post_vet['StartTime'] = $starttime->format('g:i a');
                            $name = explode(' ', $entry->getContent()->customerName);
                            $post_vet['customerFirstName'] = $name[0];
                            $post_vet['customerLastName'] = $name[1];
                            $post_vet['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
                            $post_vet['customerIdNumber'] = $entry->getContent()->customerIdNumber;
                            $post_vet['customerStreetAddress1'] = $entry->getContent()->customerStreetAddress1;
                            $post_vet['customerStreetAddress2'] = $entry->getContent()->customerStreetAddress2;
                            $post_vet['customerCity'] = $entry->getContent()->customerCity;
                            $post_vet['customerState'] = $entry->getContent()->customerstate;
                            $post_vet['customerZipCode'] = $entry->getContent()->customerZipCode;
                            $post_vet['vetID'] = $entry->getContent()->assignedVeterinarianIdNumber;
                            $post_vet['emailType'] = "vetApptReminder";
                            $post_vet['textType'] = "vet24hrsApptNotificationTxt";
                            $vetname = explode(' ', $entry->getContent()->assignedVeterinarianName);
                            $post_vet['vetFirstName'] = $vetname[0];
                            $post_vet['CustID'] = $entry->getContent()->customerIdNumber;
                            $user = craft()->users->getUserById(substr($post_vet['vetID'], 3));
                            $vetEmail = $user->username;
                            $vetCell = $user->cellPhone;
                            $post_vet['TextTo'] = $vetCell;
                            $post_vet['vetEmail'] = $vetEmail;

                            craft()->emailUser->sendEmail($post_vet);
                            $log_message = $entry->getContent()->appointmentIdNumber . ' ' . $starttime . ' > ' . $now . ' ' . $post_vet['emailType'] . ' ' . $starttime->format('Y-m-d H:i') . ' ' . $now->format('Y-m-d H:i') . ' Day:' . $dtDiff->format("%d") . ' Hours:' . $dtDiff->format("%H");
                            EmailUserPlugin::log($log_message, LogLevel::Info, true);

                            /* send 24 hours customer reminder */
                            $log_message = 'Send customer 24 hour reminder ' . $entry->getContent()->appointmentIdNumber . ' ' . $starttime->format('Y-m-d H:i') . ' ' . $now->format('Y-m-d H:i') . ' Day:' . $dtDiff->format("%d") . ' Hours:' . $dtDiff->format("%H");
                            EmailUserPlugin::log($log_message, LogLevel::Info, true);
                            $post['appointmentStartTime'] = $starttime->format('Y-m-d g:i a');
                            $post['appointmentLongDate'] = $starttime->format('l, F d Y');
                            $name = explode(' ', $entry->getContent()->customerName);
                            $post['customerFirstName'] = $name[0];
                            $post['StartTime'] = $starttime->format('g:i a');
                            $post['appointmentEndTime'] = $starttime->modify('+90 minutes')->format('g:i a');
                            $post['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
                            $post['customerIdNumber'] = $entry->getContent()->customerIdNumber;
                            $post['customerStreetAddress1'] = $entry->getContent()->customerStreetAddress1;
                            $post['customerStreetAddress2'] = $entry->getContent()->customerStreetAddress2;
                            $post['customerCity'] = $entry->getContent()->customerCity;
                            $post['customerState'] = $entry->getContent()->customerstate;
                            $post['customerZipCode'] = $entry->getContent()->customerZipCode;
                            $post['emailType'] = "custApptReminder";
                            $post['textType'] = "custApptReminderTxt";
                            $post['customeremail'] = $entry->getContent()->customeremail;
                            $post['vetName'] = $entry->getContent()->assignedVeterinarianName;
                            $post['CustID'] = $entry->getContent()->customerIdNumber;
                            $post['TextTo'] = $entry->getContent()->cellPhone;
                            craft()->emailUser->sendEmail($post);
                            $log_message = "custApptReminder " . $now . ' ' . $entry->getContent()->appointmentIdNumber . ' ' . $starttime . ' > ' . $now . ' ' . $post['emailType'] . ' ' . $starttime->format('Y-m-d H:i') . ' ' . $now->format('Y-m-d H:i') . ' Day:' . $dtDiff->format("%d") . ' Hours:' . $dtDiff->format("%H");
                            EmailUserPlugin::log($log_message, LogLevel::Info, true);

                            /* send 24 hours customer appointment text */
                            if ($entry->getContent()->txtupdates == 'updates') {
                                try {
                                    craft()->twilioSms->sendSms($post);
                                    $log_message = $entrystatus . ' send sms to ' . $post['TextTo'] . ' type: ' . $post['textType'] . ' 24 hours customer reminder';
                                    EmailUserPlugin::log($log_message, LogLevel::Info, true);
                                    TwilioSmsPlugin::log($log_message, LogLevel::Info, true);
                                } catch (Exception $e) {
                                    $log_message = 'Error in send sms';
                                    TwilioSmsPlugin::log($log_message, LogLevel::Info, true);
                                }
                            }

                        }
                    } else if (($dtDiff->format("%d") == '0') && ($dtDiff->format("%H") <= '12')) {
                        /* check appointment record */
                        if ($entrystatus == 'unavailable') {
                            /* update appointment record cancel status to 'cancelled' */

                            $uid = $entry->getContent()->customerIdNumber;
                            $custid = explode('-', $uid);
                            $customeruser = craft()->users->getUserById($custid[2]);
                            $canellationCreditMatrix = craft()->fields->getFieldByHandle("cancellation_credit");
                            $block = new MatrixBlockModel();
                            $block->fieldId = $canellationCreditMatrix->id; // Matrix field's ID
                            $block->ownerId = $customeruser->id; // ID of entry the block should be added to
                            $canellationCreditMatrixBlocks = craft()->matrix->getBlockTypesByFieldId($canellationCreditMatrix->id);
                            foreach ($canellationCreditMatrixBlocks as $key => $data) {
                                if ($data->handle === "cancellation_credit") {
                                    $creditBlock = $data;
                                    break;
                                };
                            };
                            $block->typeId = $creditBlock->id; // ID of block type
                            $block->getContent()->setAttributes(array(
                                'used_status' => "unused",
                                'real_id' => $entry->id,
                                'appt_id' => $entry->appointmentIdNumber
                            ));
                            $success = craft()->matrix->saveBlock($block);

                            $entry->setContentFromPost(['cancel_status' => "sys_cancelled"]);
                            craft()->entries->saveEntry($entry);

                            /* send 12 hours cancellation by system if vet is not assign , custApptCancelledByVet*/
                            $post['appointmentStartTime'] = $starttime->format('l F d, Y') . ' at ' . $starttime->format(' g:i a');
                            $post['appointmentLongDate'] = $starttime->format('l, F d Y');
                            $custName = explode(' ', $entry->getContent()->customerName);
                            $post['customerFirstName'] = $custName[0];
                            $post['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
                            $post['customerIdNumber'] = $entry->getContent()->customerIdNumber;
                            $post['CustID'] = $entry->getContent()->customerIdNumber;
                            $post['emailType'] = "custApptCancelledByVet";
                            $post['textType'] = "custApptCancelledTxt";
                            $post['customeremail'] = $entry->getContent()->customeremail;
                            $post['TextTo'] = $entry->getContent()->cellPhone;
                            craft()->emailUser->sendEmail($post);
                            $log_message = 'Send customer appt cancelled message ' . $entry->getContent()->appointmentIdNumber . ' ' . $starttime->format('Y-m-d H:i') . ' ' . $now->format('Y-m-d H:i') . ' Day:' . $dtDiff->format("%d") . ' Hours:' . $dtDiff->format("%H");
                            EmailUserPlugin::log($log_message, LogLevel::Info, true);

                            /* send 12 hours cancellation by system if vet is not assign , adminApptCancelledBySys */
                            $post_admin['appointmentStartTime'] = $starttime->format('Y-m-d g:i a');
                            $post_admin['appointmentLongDate '] = $starttime->format('l, F d Y');
                            $post_admin['customerName'] = $entry->getContent()->customerName;
                            $post_admin['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
                            $post_admin['customerIdNumber'] = $entry->getContent()->customerIdNumber;
                            $post_admin['CustID'] = $entry->getContent()->customerIdNumber;
                            $post_admin['emailType'] = "adminApptCancelledBySys";
                            $post_admin['customeremail'] = $entry->getContent()->customeremail;
                            craft()->emailUser->sendEmail($post_admin);
                            $log_message = 'Send admin adminApptCancelledBySys ' . $entry->getContent()->appointmentIdNumber . ' ' . $starttime->format('Y-m-d H:i') . ' ' . $now->format('Y-m-d H:i') . ' Day:' . $dtDiff->format("%d") . ' Hours:' . $dtDiff->format("%H");
                            EmailUserPlugin::log($log_message, LogLevel::Info, true);

                            /* send 12 hours cancellation on text to customer*/
                            if ($entry->getContent()->txtupdates == 'updates') {
                                try {
                                    craft()->twilioSms->sendSms($post);
                                    $log_message = $entrystatus . ' send sms to ' . $post['TextTo'] . ' type: ' . $post['textType'];
                                    TwilioSmsPlugin::log($log_message, LogLevel::Info, true);
                                } catch (Exception $e) {
                                    $log_message = 'Error in send sms';
                                    TwilioSmsPlugin::log($log_message, LogLevel::Info, true);
                                }
                            }
                        }
                    } else {
                        $log_message = $entry->getContent()->appointmentIdNumber . ' ' . $starttime . ' > ' . $now . ' Didnt send any reminder ' . $starttime->format('Y-m-d H:i') . ' ' . $now->format('Y-m-d H:i') . ' Year:' . $dtDiff->format("%y") . ' Month:' . $dtDiff->format("%m") . ' Day:' . $dtDiff->format("%d") . ' Hours:' . $dtDiff->format("%H");
                        EmailUserPlugin::log($log_message, LogLevel::Info, true);
                    }
                } else {
                    if (($dtDiff->format("%y") == 0) && ($dtDiff->format("%m") == 0) && ($dtDiff->format("%d") == 1) && ($dtDiff->format("%H") == 0)) {
                        if (($entrystatus == 'rebooked') || ($entrystatus == '')) {
                            /* send feedback survey after 24 hours */
                            $name = explode(' ', $entry->getContent()->customerName);
                            $post['customerFirstName'] = $name[0];
                            $post['vetID'] = $entry->getContent()->assignedVeterinarianIdNumber;
                            $post['customerIdNumber'] = $entry->getContent()->customerIdNumber;
                            $post['appointmentIdNumber'] = $entry->getContent()->appointmentIdNumber;
                            $post['CustID'] = $entry->getContent()->customerIdNumber;
                            $post['emailType'] = "custFeedback";
                            $post['customeremail'] = $entry->getContent()->customeremail;
                            $vetname = explode(' ', $entry->getContent()->assignedVeterinarianName);
                            $post['vetFirstName'] = $vetname[0];
                            $post['vid'] = substr($post['vetID'], 3);
                            craft()->emailUser->sendEmail($post);
                            $log_message = 'Send Feedback Survey ' . $entry->getContent()->appointmentIdNumber . ' ' . $starttime . ' ' . $now . ' Year:' . $dtDiff->format("%y") . ' Month:' . $dtDiff->format("%m") . ' Day:' . $dtDiff->format("%d") . ' Hours:' . $dtDiff->format("%H");
                            EmailUserPlugin::log($log_message, LogLevel::Info, true);
                        }
                    } else {
                        $log_message = $entry->getContent()->appointmentIdNumber . ' ' . $starttime . ' < ' . $now . ' Didnt send any survey ' . $starttime->format('Y-m-d H:i') . ' ' . $now->format('Y-m-d H:i') . ' Day:' . $dtDiff->format("%d") . ' Hours:' . $dtDiff->format("%H");
                        EmailUserPlugin::log($log_message, LogLevel::Info, true);
                    }
                }
                $log_message = $entry->getContent()->appointmentIdNumber . ' ' . $starttime . ' > ' . $now . ' Didnt send any reminder ' . $starttime->format('Y-m-d H:i') . ' ' . $now->format('Y-m-d H:i') . ' Year:' . $dtDiff->format("%y") . ' Month:' . $dtDiff->format("%m") . ' Day:' . $dtDiff->format("%d") . ' Hours:' . $dtDiff->format("%H");
                EmailUserPlugin::log($log_message, LogLevel::Info, true);
            }
        } else {
            // No entry found that matches criteria.
        }

        /* to avoid jquery 404 error */
        die();
    }

    public function actionAnnualReminder()
    {
        /* Send Annual reminder to make appt soon */
        /* Get last year appointment records */

        $queryStartDate = date('Y-m-d', strtotime("-365 days"));

        $criteria = craft()->elements->getCriteria(ElementType::Entry);
        $criteria->section = 'appointmentRecords';
        //$criteria->postDate = array('and', '>='.$queryStartDate, '<'.$queryEndDate);
        $criteria->postDate = '>=' . $queryStartDate;
        $criteria->limit = null;
        $entries = $criteria->find();
        $post['emailType'] = "custMakeApptSoon";


        //create an array of unique customers and their most recent appointment
        $mostRecentAppts = array();
        if ($entries) {
            foreach ($entries as $entry) {
                $entrystatus = trim($entry->getContent()->cancel_status);
                if (($entrystatus == 'rebooked') || ($entrystatus == '')) {

                    //if you find a record in mostRecentAppts, check to see if the new
                    //entry is more recent and if so update mostRecentAppts
                    if(array_key_exists($entry->customerRealId, $mostRecentAppts)){
                        $mostRecentApptStartTime = new \DateTime($mostRecentAppts[$entry->customerRealId]->appointmentStartTime);
                        $entryStartTime = new \DateTime($entry->appointmentStartTime);
                        $dtDiff = $mostRecentApptStartTime->diff($entryStartTime)->format("%r%a");


                        //if the entry is more recent then what is stored in mostRecentAppts,
                        //replace the mostRecentAppts entry with it
                        if ($dtDiff > '0'){
                            $mostRecentAppts[$entry->customerRealId] = $entry;
                        }
                    }
                    else{
                        //if this key is not in mostRecentAppts, then this entry is new and
                        //thus is the most recent
                        $mostRecentAppts[$entry->customerRealId] = $entry;
                    }
                }
            }
        }

        if ($mostRecentAppts) {
            foreach ($mostRecentAppts as $entry) {
                $entrystatus = trim($entry->getContent()->cancel_status);
                if (($entrystatus == 'rebooked') || ($entrystatus == '')) {
                    $starttime = new \DateTime($entry->appointmentStartTime);
                    $now = new \DateTime();
                    $dtDiff = $starttime->diff($now)->format("%a");
                    $pets_name = '';
                    $pet = array();


                    /* achan  modify diff to 3 day for testing , original is 345 days*/
                    /* achan 05-09-2019 change back to 345 */
                    if ($dtDiff == '345') {

                        /* send custMakeApptSoon */

                        /* get pets name */
                        $matrixCriteria = craft()->elements->getCriteria(\Craft\ElementType::MatrixBlock);
                        $matrixCriteria->type = 'pet';
                        $matrixCriteria->ownerId = $entry->id;
                        $matrixPets = $matrixCriteria->find();

                        /* get decreased info from user profile */
                        $user_criteria = craft()->elements->getCriteria(ElementType::User);
                        $user_criteria->id = $entry->customerRealId;
                        $user_criteria->limit = 1;
                        $user_infos = $user_criteria->find();
                        foreach ($user_infos as $user_info) {
                            $user_pets = $user_info->pets;
                            foreach ($user_pets as $user_pet) {
                                $pet[$user_pet->petFirstName]['deceased'] = $user_pet->deceased;
                            }
                        }

                        $count = 0;
                        $send = false;
                        $deceased_pet = '';
                        if ($matrixPets) {
                            foreach ($matrixPets as $matrixPet) {
                                /* only pet is not decreased to set true and send out the email include the pet's name */
                                if ($pet[$matrixPet->petFirstName]['deceased'] == 0) {
                                    if ($count == 0) {
                                        $pets_name = $matrixPet->petFirstName;
                                    } else {
                                        $pets_name = $pets_name . ', ' . $matrixPet->petFirstName;
                                    }
                                    $count++;
                                    $send = true; /* turn to true when any pet is not in deceased */
                                } else {
                                    $deceased_pet = $deceased_pet . ' ' . $matrixPet->petFirstName;
                                }
                            }
                        }
                        $post['pets_name'] = $pets_name;
                        $name = explode(' ', $entry->customerName);
                        $post['customerFirstName'] = $name[0];
                        $post['vetID'] = $entry->assignedVeterinarianIdNumber;
                        $post['customerIdNumber'] = $entry->customerIdNumber;
                        $post['customeremail'] = $entry->customeremail;

                        if ($send) {
                            craft()->emailUser->sendEmail($post);
                            $log_message = 'Day count: ' . $count . ' email type: ' . $post['emailType'] . ' appt id ' . $entry->appointmentIdNumber . ' Petname: ' .
                                $pets_name . ' entry id ' . $entry->id . ' start time: ' . $starttime->format('Y-m-d') . ' current date ' . $now->format('Y-m-d') . ' date difference: ' . $dtDiff;
                            EmailUserPlugin::log($log_message, LogLevel::Info, true);
                        } else {
                            /* don't send any message */
                            $log_message = 'Pets ' . $deceased_pet . ' in deceased, no email send';;
                            EmailUserPlugin::log($log_message, LogLevel::Info, true);
                        }

                    }
                    $log_message = ' date diff: '.$dtDiff . ' appt number: ' . $entry->appointmentIdNumber . ' entry status' . $entrystatus;
                EmailUserPlugin::log($log_message, LogLevel::Info, true);
              } else {
                    $log_message = ' appt id ' . $entry->appointmentIdNumber . ' entry cancel status ' . $entry->cancel_status . ' No email has been sent';
                    EmailUserPlugin::log($log_message, LogLevel::Info, true);
                }
            }
        }
        /* to avoid jquery 404 error */
        die();
    }

    public function actionValidateHoldAppt(){
        $log_message = 'Call Validate Hold Appt';
        EmailUserPlugin::log($log_message, LogLevel::Info, true);

        /* Get all future appt */
        $today = date('Y-m-d');
        $ARcriteria = craft()->elements->getCriteria(ElementType::Entry);
        $ARcriteria->section = 'appointmentRecords';
        $ARcriteria->appointmentStartTime = '>=' . $today;
        $ARcriteria->limit = null;
        $ARentries = $ARcriteria->find();

        /* Get all hold appointment */
        $HAcriteria = craft()->elements->getCriteria(ElementType::Entry);
        $HAcriteria->section = 'holdappointment';
        $HAcriteria->timeslot = '>=' . $today;
        $HAcriteria->limit = null;
        $HAentries = $HAcriteria->find();

        if ($ARentries) {
            $log_message = 'Has Appointment Record in the future.';
            EmailUserPlugin::log($log_message, LogLevel::Info, true);


            /* foreach Appointment Record to check Hold ID and hold appointment match */
            foreach ($ARentries as $ARentry) {
                /* Cancel status skip the email */
                if ($ARentry->getcontent()->cancel_status == 'customer_cancelled'){
                    $appointmentIdNumber = $ARentry->appointmentIdNumber;
                    $hold_id = trim($ARentry->getcontent()->holdEntryId);
                    $log_message = 'Hold ID: '.$hold_id. ' Appt Number: '.$appointmentIdNumber. ' has customer cancelled status';
                    EmailUserPlugin::log($log_message, LogLevel::Info, true);
                }else{
                    /* reset checkhold flag, $holdFlag = 0 (no hold find), $holdflag = 1 (hold slot has appointment match) */
                    $holdFlag = 0;

                    $starttime = new DateTime($ARentry->getContent()->appointmentStartTime);
                    $appointmentIdNumber = $ARentry->appointmentIdNumber;
                    $hold_id = trim($ARentry->getcontent()->holdEntryId);
                    $appt_starttime = trim($ARentry->getcontent()->appointmentStartTime);
                    $log_message = 'appt date '.$starttime.' Hold '.$hold_id.' entry';
                    EmailUserPlugin::log($log_message, LogLevel::Info, true);

                    if (($hold_id == '') || (!isset($hold_id)) || ($hold_id == 0)) {
                        /* email to admin */
                        $post['emailType'] = "adminNoHoldID";
                        $post['appointmentIdNumber'] = trim($ARentry->getcontent()->appointmentIdNumber);
                        $log_message = 'No Hold ID in appointment, send email to Admin to follow up.';
                        EmailUserPlugin::log($log_message, LogLevel::Info, true);
                        craft()->emailUser->sendEmail($post);
                    }

                    /* check with hold appt */
                    if ($HAentries) {
                        foreach ($HAentries as $HAentry) {
                            $hold_timeslot = trim($HAentry->getcontent()->timeslot);
                            $hold_entry_id = $HAentry->id;
                            $log_message = 'Hold ID '.$hold_id.' Hold entry ID: '.$hold_entry_id.' timeslot '.$hold_timeslot;
                            EmailUserPlugin::log($log_message, LogLevel::Info, true);
                            if ($hold_id == $hold_entry_id){
                                $holdFlag = 1; /* Turn on flag when there's hold ID match with appointment hold ID */
                                $log_message = 'Appt id '.$appointmentIdNumber.' Hold entry ID match: '.$hold_entry_id.' timeslot '.$hold_timeslot;
                                EmailUserPlugin::log($log_message, LogLevel::Info, true);
                            }
                        }
                    }
                    /* For each appointment, if $holdFlag = 0, email to Admin */
                    if (($holdFlag == 0 ) && ($hold_id != 0))  {
                        $post['emailType'] = "adminHoldingApptMissing";
                        $post['appointmentIdNumber'] = trim($ARentry->getcontent()->appointmentIdNumber);
                        $log_message = 'ApptID '.$appointmentIdNumber.' Hold ID '.$hold_id.' Hold entry ID: '.$hold_entry_id.' No hold has been found, send email to admin to follow up. ';
                        EmailUserPlugin::log($log_message, LogLevel::Info, true);
                        craft()->emailUser->sendEmail($post);
                    }
                }

            }
        }
        /* to avoid jquery 404 error */
        die();
    }
}

