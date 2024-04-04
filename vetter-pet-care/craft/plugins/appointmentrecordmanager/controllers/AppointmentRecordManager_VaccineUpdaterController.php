<?php
/**
 * Appointment Record Manager plugin for Craft CMS
 *
 * AppointmentRecordManager_VaccineUpdater Controller
 *
 * This class acts as the middle man between the front end and the service Appointment Record manager service
 * layer.
 *
 * @author    Kenneth Huebsch
 * @package   AppointmentRecordManager
 * @since     1.0.0
 */

namespace Craft;

class AppointmentRecordManager_VaccineUpdaterController extends BaseController
{

    /**
     * @var    bool|array Allows anonymous access to this controller's actions.
     * @access protected
     */
    protected $allowAnonymous = array('actionIndex',
        );

    /**
     * receive the vacc data from the form submission and call the service to make the updates
     */
    public function actionUpdateVaccDates()
    {
	$customerRealId = craft()->request->getRequiredParam('customerRealId');
	$selectedPet = craft()->request->getRequiredParam('selectedPet');
	$weight = craft()->request->getRequiredParam('weight');
	$color = craft()->request->getRequiredParam('color');
	$canineRabiesDateGiven = craft()->request->getRequiredParam('canineRabiesDateGiven');
	$canineRabiesNextDue = craft()->request->getRequiredParam('canineRabiesNextDue');
	$da2ppDateGiven = craft()->request->getRequiredParam('da2ppDateGiven');
	$da2ppNextDue = craft()->request->getRequiredParam('da2ppNextDue');
	$leptospirosisDateGiven = craft()->request->getRequiredParam('leptospirosisDateGiven');
	$leptospirosisNextDue = craft()->request->getRequiredParam('leptospirosisNextDue');
	$bordetellaDateGiven = craft()->request->getRequiredParam('bordetellaDateGiven');
	$bordetellaNextDue = craft()->request->getRequiredParam('bordetellaNextDue');
	$influenzaDateGiven = craft()->request->getRequiredParam('influenzaDateGiven');
	$influenzaNextDue = craft()->request->getRequiredParam('influenzaNextDue');
	$lymeDateGiven = craft()->request->getRequiredParam('lymeDateGiven');
	$lymeNextDue = craft()->request->getRequiredParam('lymeNextDue');
	$negativeHwtDateGiven = craft()->request->getRequiredParam('negativeHwtDateGiven');
	$negativeHwtNextDue = craft()->request->getRequiredParam('negativeHwtNextDue');
	$felineRabiesDateGiven = craft()->request->getRequiredParam('felineRabiesDateGiven');
	$felineRabiesNextDue = craft()->request->getRequiredParam('felineRabiesNextDue');
	$fvrcpDateGiven = craft()->request->getRequiredParam('fvrcpDateGiven');
	$fvrcpNextDue = craft()->request->getRequiredParam('fvrcpNextDue');
	$felvDateGiven = craft()->request->getRequiredParam('felvDateGiven');
	$felvNextDue = craft()->request->getRequiredParam('felvNextDue');

	craft()->appointmentRecordManager_dataManager->updateVaccDates($customerRealId,
				                                       $selectedPet,
								       $weight,
                                                                       $color,
				                                       $canineRabiesDateGiven,
				                                       $canineRabiesNextDue,
				                                       $da2ppDateGiven,
				                                       $da2ppNextDue,
				                                       $leptospirosisDateGiven,
				                                       $leptospirosisNextDue,
				                                       $bordetellaDateGiven,
				                                       $bordetellaNextDue,
				                                       $influenzaDateGiven,
				                                       $influenzaNextDue,
				                                       $lymeDateGiven,
				                                       $lymeNextDue,
				                                       $negativeHwtDateGiven,
				                                       $negativeHwtNextDue,
				                                       $felineRabiesDateGiven,
				                                       $felineRabiesNextDue,
				                                       $fvrcpDateGiven,
				                                       $fvrcpNextDue,
				                                       $felvDateGiven,
				                                       $felvNextDue);
	
    }
}
