<?php
/**
 * Appointment Record Manager plugin for Craft CMS
 *
 * AppointmentRecordManager_PdfFiller Controller
 *
 * @author    Kenneth Huebsch
 * @package   AppointmentRecordManager
 * @since     1.0.0
 */

namespace Craft;

abstract class PdfType
{
	const PatientEvaluation = 0;
	const VaccinationRecord = 1;
}

class AppointmentRecordManager_PdfFillerController extends BaseController
{

		//data array used to fill fdf
		private $data;

		//where the new filled pdf will be saved
		private $output;

		//path of the fillable url
		private $pdfurl;

		//name of the file when it downloads
		private $filename;

		//returns a unique file name in craft's temp folder with the extension
		//passed in as an argument
		private function tmpfile($ext) {
			return tempnam(craft()->path->getTempPath(), '_pdffiller'.$ext);
		}

		//donloads the file to the browser using the name passed in as an argument
		private function download(){
			$filepath = $this->output;
			craft()->request->sendFile($this->filename.'.pdf', file_get_contents($filepath), array('forceDownlad' => true));
			unlink($this->output);
		}

		//creates the fdf file which is used to fill the pdf form
		private function makeFdf($data) {
			//define pdf version and fields
			$fdf = '%FDF-1.2
			1 0 obj<</FDF<< /Fields[';

			//write each item in the data struct to the file
			foreach ($data as $key => $value) {
				//add fields value
				$fdf .= '<</T(' . $key . ')/V(' . $value . ')>>';
			}
			$fdf .= "] >> >>
			endobj
			trailer
			<</Root 1 0 R>>
			%%EOF";

			//store the string in a temp file
			$fdf_file = $this->tmpfile('fdf');
			file_put_contents($fdf_file, $fdf);

			return $fdf_file;
		}

		//generates the pdf, using the fdf passed in
		private function generate($fdf) {
			//pick a name in the temp folder to place the filled-in pdf
			$this->output = $this->tmpfile('pdf');

			//execute the form process
			exec("pdftk {$this->pdfurl} fill_form {$fdf} output {$this->output}");

			//now that the pdf is created, no need to hold onto the fdf
			unlink($fdf);
		}

		//finds the entry that matches the appointmentIdNumber and populates data
		private function populateData($pdfType)
		{
			//gather necessary data
			$appointmentIdNumber = craft()->request->getRequiredParam('appointmentIdNumber');
			$selectedPet = craft()->request->getRequiredParam('selectedPet');
			$criteria = craft()->elements->getCriteria(ElementType::Entry);
			$criteria->appointmentIdNumber = $appointmentIdNumber;
			$entry = $criteria->first();
			$customer = craft()->users->getUserById($entry->customerRealId);
			$this->data = [];
			
			//find the appointment's pet
			$apptPet = NULL;
			$apptPetsFound = 0;;
			if($entry != NULL)
			{
				$apptPet = $entry->pets[0];
				foreach($entry->pets as $block)
				{
					if($block->petFirstName == $selectedPet)
					{
						$apptPet = $block;
						$apptPetsFound = $apptPetsFound + 1;
					}
				}

			}
			else
			{
				//if you cant find the entry, don't bother continuing
				die();
			}

			//find the profile pet
			$profilePet = NULL;
			$profilePetsFound = 0;
			if($customer != NULL)
			{
				$profilePet = $customer->pets[0];
				foreach($customer->pets as $block)
				{
					if($block->petFirstName == $selectedPet)
					{
						$profilePet = $block;
						$profilePetsFound = $profilePetsFound + 1;
					}
				}
			}
			
			//populate data for patient eval
			if($pdfType == PdfType::PatientEvaluation)
			{
				if($entry != NULL)
				{
					//we should have already died if entry is null. this is a bit redundant
					$apptDate = new DateTime(DateTimeHelper::nice($entry->appointmentDate));
					$startTime = new DateTime($entry->appointmentStartTime);
					$this->pdfurl = craft()->path->getPluginsPath().'appointmentrecordmanager/resources/patient-evaluation.pdf';
					$this->filename = $selectedPet.'_'.($customer['lastName'] ? $customer['lastName'] : '').'_Eval_'.($apptDate ? $apptDate->format('d-M-Y'): '');

					$this->data['Appt  ID'] = $entry->appointmentIdNumber;
					$this->data['Date'] = ($apptDate ? $apptDate->format('n/j/Y'): '');
					$this->data['Time'] = ($startTime ? $startTime->format('H:ia') : '');
					$this->data['Customer ID'] = $entry->customerIdNumber;
					$this->data['Vet  Name'] = $entry->assignedVeterinarianName;
				}

				if($apptPetsFound == 1)
				{
					$dob = new DateTime($apptPet->birthdate);
					$this->data['AgeDOB'] = ($dob ? $dob->format('n/j/Y') : '');
					$this->data['Pet  Name'] = $apptPet->petFirstName;
					$this->data['Species'] = $apptPet->species;
					$this->data['Breed'] = $apptPet->breed;
					$this->data['SpayedNeutered YN'] = $apptPet->spayedOrNeutered;
					$this->data['Sex MF'] = $apptPet->gender;

				}

				if($customer != NULL)
				{
					$this->data['Owner Last  Name'] = $customer['lastName'];
				}

				if($profilePetsFound == 1)
				{
				       $this->data['Color'] = $profilePet->color;
				       $this->data['Weight'] = ($profilePet->weight == 0 ? '' : $profilePet->weight );
				       $this->data['Date GivenRow1'] = ($profilePet->canineRabiesDateGiven ? $profilePet->canineRabiesDateGiven->format('n/j/Y') : '');
				       $this->data['Next\t DueRow1'] = ($profilePet->canineRabiesNextDue ? $profilePet->canineRabiesNextDue->format('n/j/Y') : '');
				       $this->data['Date GivenRow2'] = ($profilePet->da2ppDateGiven ? $profilePet->da2ppDateGiven->format('n/j/Y') : '');
				       $this->data['Next\t DueRow2'] = ($profilePet->da2ppNextDue ? $profilePet->da2ppNextDue->format('n/j/Y') : '');
				       $this->data['Date GivenRow3'] = ($profilePet->leptospirosisDateGiven ? $profilePet->leptospirosisDateGiven->format('n/j/Y') : '');
				       $this->data['Next\t DueRow3'] = ($profilePet->leptospirosisNextDue ? $profilePet->leptospirosisNextDue->format('n/j/Y') : '');
				       $this->data['Date GivenRow4'] = ($profilePet->bordetellaDateGiven ? $profilePet->bordetellaDateGiven->format('n/j/Y') : '');
				       $this->data['Next\t DueRow4'] = ($profilePet->bordetellaNextDue ? $profilePet->bordetellaNextDue->format('n/j/Y') : '');
				       $this->data['Date GivenRow5'] = ($profilePet->influenzaDateGiven ? $profilePet->influenzaDateGiven->format('n/j/Y') : '');
				       $this->data['Next\t DueRow5'] = ($profilePet->influenzaNextDue ? $profilePet->influenzaNextDue->format('n/j/Y') : '');
				       $this->data['Date GivenRow6'] = ($profilePet->lymeDateGiven ? $profilePet->lymeDateGiven->format('n/j/Y') : '');
				       $this->data['Next\t DueRow6'] = ($profilePet->lymeNextDue ? $profilePet->lymeNextDue->format('n/j/Y') : '');
				       $this->data['Date GivenRow1_2'] = ($profilePet->felineRabiesDateGiven ? $profilePet->felineRabiesDateGiven->format('n/j/Y') : '');
				       $this->data['Next\t DueRow1_2'] = ($profilePet->felineRabiesNextDue ? $profilePet->felineRabiesNextDue->format('n/j/Y') : '');
				       $this->data['Date GivenRow2_2'] = ($profilePet->fvrcpDateGiven ? $profilePet->fvrcpDateGiven->format('n/j/Y') : '');
				       $this->data['Next\t DueRow2_2'] = ($profilePet->fvrcpNextDue ? $profilePet->fvrcpNextDue->format('n/j/Y') : '');
				       $this->data['Date GivenRow3_2'] = ($profilePet->felvDateGiven ? $profilePet->felvDateGiven->format('n/j/Y') : '');
				       $this->data['Next\t DueRow3_2'] = ($profilePet->felvNextDue ? $profilePet->felvNextDue->format('n/j/Y') : '');
				       $this->data['Negative HWT'] = ($profilePet->negativeHwtDateGiven ? $profilePet->negativeHwtDateGiven->format('n/j/Y') : '');
				       $this->data['Negative HWT Next Due'] = ($profilePet->negativeHwtNextDue ? $profilePet->negativeHwtNextDue->format('n/j/Y') : '');
				}
			}//end patient eval
			elseif($pdfType == PdfTYpe::VaccinationRecord)
			{
				$vet = NULL;
				if($entry != NULL)
				{
					$apptDate = new DateTime(DateTimeHelper::nice($entry->appointmentDate));
					$this->pdfurl = craft()->path->getPluginsPath().'appointmentrecordmanager/resources/vaccination-record.pdf';
					$this->filename = $selectedPet.'_'.($customer['lastName'] ? $customer['lastName'] : '').'_VaxRecord_'.($apptDate ? $apptDate->format('d-M-Y'): '');
					$vet = craft()->users->getUserById(substr($entry->assignedVeterinarianIdNumber, 3));
					$address2 = $entry->customerStreetAddress2;
					$address3 = $entry->customerCity.', '.$entry->customerstate.' '.$entry->customerZipCode;
					if($address2 == '')
					{
						$address2 = $address3;
						$address3 = '';
					}

					$this->data['Date'] = ($apptDate ? $apptDate->format('n/j/Y'): '');
					$this->data['Address 1'] = $entry->customerStreetAddress1;
					$this->data['Address 2'] = $address2;
					$this->data['Address 3'] = $address3;

				}

				if($customer != NULL)
				{
					$this->data['Owner'] = $entry->customerName;
				}

				if($apptPetsFound == 1)
				{
					$dob = new DateTime($apptPet->birthdate);
					$this->data['Age' ] = ($dob ? $dob->format('n/j/Y') : '');
					$this->data['Pet  Name' ] = $apptPet->petFirstName;
					$this->data['Species' ] = $apptPet->species;
					$this->data['Breed' ] = $apptPet->breed;
					$this->data['Sex' ] = $apptPet->gender;


				}

				if($vet != NULL)
				{
					$this->data['License'] = $vet->licenseNumber;
				}

				if($profilePetsFound == 1)
				{
					$this->data['Markings' ] = $profilePet->color;
					$this->data['Rabies' ] = ($profilePet->canineRabiesDateGiven ? $profilePet->canineRabiesDateGiven->format('n/j/Y') : '');
					$this->data['Next  Due 1' ] = ($profilePet->canineRabiesNextDue ? $profilePet->canineRabiesNextDue->format('n/j/Y') : '');
					$this->data['Distemper' ] = ($profilePet->da2ppDateGiven ? $profilePet->da2ppDateGiven->format('n/j/Y') : '');
					$this->data['Next  Due 2' ] = ($profilePet->da2ppNextDue ? $profilePet->da2ppNextDue->format('n/j/Y') : '');
					$this->data['Leptospirosis' ] = ($profilePet->leptospirosisDateGiven ? $profilePet->leptospirosisDateGiven->format('n/j/Y') : '');
					$this->data['Next  Due 3' ] = ($profilePet->leptospirosisNextDue ? $profilePet->leptospirosisNextDue->format('n/j/Y') : '');
					$this->data['Bordetella' ] = ($profilePet->bordetellaDateGiven ? $profilePet->bordetellaDateGiven->format('n/j/Y') : '');
					$this->data['Next  Due 4' ] = ($profilePet->bordetellaNextDue ? $profilePet->bordetellaNextDue->format('n/j/Y') : '');
					$this->data['Influenza' ] = ($profilePet->influenzaDateGiven ? $profilePet->influenzaDateGiven->format('n/j/Y') : '');
					$this->data['Next  Due 5' ] = ($profilePet->influenzaNextDue ? $profilePet->influenzaNextDue->format('n/j/Y') : '');
					$this->data['Lyme' ] = ($profilePet->lymeDateGiven ? $profilePet->lymeDateGiven->format('n/j/Y') : '');
					$this->data['Next  Due 6' ] = ($profilePet->lymeNextDue ? $profilePet->lymeNextDue->format('n/j/Y') : '');
					$this->data['Rabies_2' ] = ($profilePet->felineRabiesDateGiven ? $profilePet->felineRabiesDateGiven->format('n/j/Y') : '');
					$this->data['Next  Due 1_2' ] = ($profilePet->felineRabiesNextDue ? $profilePet->felineRabiesNextDue->format('n/j/Y') : '');
					$this->data['FVRCP' ] = ($profilePet->fvrcpDateGiven ? $profilePet->fvrcpDateGiven->format('n/j/Y') : '');
					$this->data['Next  Due 2_2' ] = ($profilePet->fvrcpNextDue ? $profilePet->fvrcpNextDue->format('n/j/Y') : '');
					$this->data['FeLV' ] = ($profilePet->felvDateGiven ? $profilePet->felvDateGiven->format('n/j/Y') : '');
					$this->data['Next  Due 3_2' ] = ($profilePet->felvNextDue ? $profilePet->felvNextDue->format('n/j/Y') : '');
					$this->data['NegHWT' ] = ($profilePet->negativeHwtDateGiven ? $profilePet->negativeHwtDateGiven->format('n/j/Y') : '');
					$this->data['Next HWT' ] = ($profilePet->negativeHwtNextDue ? $profilePet->negativeHwtNextDue->format('n/j/Y') : '');
				}
			}
			else
			{
				//unknown pdf type
				die();
			}
		}//end populate data

		//public method called to start the whole pdf generation process
		public function actionGeneratePatientEval()
		{
			//get necessary data from form parameters
			$this->populateData(PdfType::PatientEvaluation);

			//create the FDF file with the data obtained through form parameters
			$fdf = $this->makeFdf($this->data);

			//use the FDF file to fill the PDF
			$this->generate($fdf);

			//download the filled in PDF
			$this->download();
		}

		//public method called to start the whole pdf generation process
		public function actionGenerateVaccinationRecord()
		{
			//get necessary data from form parameters
			$this->populateData(PdfType::VaccinationRecord);

			//create the FDF file with the data obtained through form parameters
			$fdf = $this->makeFdf($this->data);

			//use the FDF file to fill the PDF
			$this->generate($fdf);

			//download the filled in PDF
			$this->download();
		}

}
