<?php
/**
 * Appointment Record Manager plugin for Craft CMS
 *
 * AppointmentRecordManager_DataManager Service
 *
 * Handles business logic associated with actions that the vets can perform on the appointment record page.
 *
 *
 * @author    Kenneth Huebsch
 * @package   AppointmentRecordManager
 * @since     1.0.0
 */

namespace Craft;

class AppointmentRecordManager_DataManagerService extends BaseApplicationComponent
{
    /**
     * This method will find the user with the associated RealId. If he/she exists, it will update the
     * vaccination dates based on the parameters.
     */
    public function updateVaccDates($customerRealId,
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
                                    $felvNextDue)
    {
	//find the user based on the customer real ID
	$customer = craft()->users->getUserById($customerRealId);
	if($customer != NULL)
	{
		//locate the selected pet
		$pet = $customer->pets[0];
		$numPetsFound = 0;
		foreach($customer->pets as $block)
		{
			if($block->petFirstName == $selectedPet)
			{
				$pet = $block;
				$numPetsFound = $numPetsFound + 1;
			}
		}

		if($numPetsFound == 1)
		{
			$data = array();

			//if the date is set, add a day to it so that it populates right in the profile
			if($weight != NULL)
			{
				$data["weight"] = $weight;
			}
			else
			{
				$data["weight"] = '';
				
			}	

			if($color != NULL)
			{
				$data["color"] = $color;
			}
			else
			{
				$data["color"] = '';
			}	

			if($canineRabiesDateGiven != NULL)
			{
				$dt = new DateTime($canineRabiesDateGiven);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["canineRabiesDateGiven"] = $dt;
			}
			else
			{
				$data["canineRabiesDateGiven"] = '';
			}	

			if($canineRabiesNextDue != NULL)
			{
				$dt = new DateTime($canineRabiesNextDue);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["canineRabiesNextDue"] = $dt;
			}
			else
			{
				$data["canineRabiesNextDue"] = '';
			}	

			if($da2ppDateGiven != NULL)
			{
				$dt = new DateTime($da2ppDateGiven);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["da2ppDateGiven"] = $dt;
			}
			else
			{
				$data["da2ppDateGiven"] = '';
			}	

			if($da2ppNextDue != NULL)
			{
				$dt = new DateTime($da2ppNextDue);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["da2ppNextDue"] = $dt;
			}
			else
			{
				$data["da2ppNextDue"] = '';
			}	

			if($leptospirosisDateGiven != NULL)
			{
				$dt = new DateTime($leptospirosisDateGiven);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["leptospirosisDateGiven"] = $dt;
			}
			else
			{
				$data["leptospirosisDateGiven"] = '';
			}	

			if($leptospirosisNextDue != NULL)
			{
				$dt = new DateTime($leptospirosisNextDue);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["leptospirosisNextDue"] = $dt;
			}
			else
			{
				$data["leptospirosisNextDue"] = '';
			}	

			if($bordetellaDateGiven != NULL)
			{
				$dt = new DateTime($bordetellaDateGiven);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["bordetellaDateGiven"] = $dt;
			}
			else
			{
				$data["bordetellaDateGiven"] = '';
			}	

			if($bordetellaNextDue != NULL)
			{
				$dt = new DateTime($bordetellaNextDue);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["bordetellaNextDue"] = $dt;
			}
			else
			{
				$data["bordetellaNextDue"] = '';
			}	

			if($influenzaDateGiven != NULL)
			{
				$dt = new DateTime($influenzaDateGiven);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["influenzaDateGiven"] = $dt;
			}
			else
			{
				$data["influenzaDateGiven"] = '';
			}	

			if($influenzaNextDue != NULL)
			{
				$dt = new DateTime($influenzaNextDue);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["influenzaNextDue"] = $dt;
			}
			else
			{
				$data["influenzaNextDue"] = '';
			}	

			if($lymeDateGiven != NULL)
			{
				$dt = new DateTime($lymeDateGiven);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["lymeDateGiven"] = $dt;
			}
			else
			{
				$data["lymeDateGiven"] = '';
			}	

			if($lymeNextDue != NULL)
			{
				$dt = new DateTime($lymeNextDue);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["lymeNextDue"] = $dt;
			}
			else
			{
				$data["lymeNextDue"] = '';
			}	

			if($negativeHwtDateGiven != NULL)
			{
				$dt = new DateTime($negativeHwtDateGiven);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["negativeHwtDateGiven"] = $dt;
			}
			else
			{
				$data["negativeHwtDateGiven"] = '';
			}	

			if($negativeHwtNextDue != NULL)
			{
				$dt = new DateTime($negativeHwtNextDue);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["negativeHwtNextDue"] = $dt;
			}
			else
			{
				$data["negativeHwtNextDue"] = '';
			}	

			if($felineRabiesDateGiven != NULL)
			{
				$dt = new DateTime($felineRabiesDateGiven);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["felineRabiesDateGiven"] = $dt;
			}
			else
			{
				$data["felineRabiesDateGiven"] = '';
			}	

			if($felineRabiesNextDue != NULL)
			{
				$dt = new DateTime($felineRabiesNextDue);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["felineRabiesNextDue"] = $dt;
			}
			else
			{
				$data["felineRabiesNextDue"] = '';
			}	

			if($fvrcpDateGiven != NULL)
			{
				$dt = new DateTime($fvrcpDateGiven);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["fvrcpDateGiven"] = $dt;
			}
			else
			{
				$data["fvrcpDateGiven"] = '';
			}	

			if($fvrcpNextDue != NULL)
			{
				$dt = new DateTime($fvrcpNextDue);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["fvrcpNextDue"] = $dt;
			}
			else
			{
				$data["fvrcpNextDue"] = '';
			}	

			if($felvDateGiven != NULL)
			{
				$dt = new DateTime($felvDateGiven);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["felvDateGiven"] = $dt;
			}
			else
			{
				$data["felvDateGiven"] = '';
			}	

			if($felvNextDue != NULL)
			{
				$dt = new DateTime($felvNextDue);
				date_add($dt, date_interval_create_from_date_string('1 day'));
				$data["felvNextDue"] = $dt;
			}
			else
			{
				$data["felvNextDue"] = '';
			}	

			$pet->getContent()->setAttributes($data);
			craft()->matrix->saveBlock($pet);
		}
	}
    }
}
