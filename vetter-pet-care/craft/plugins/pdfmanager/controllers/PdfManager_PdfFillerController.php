<?php
/**
 * PdfManager plugin for Craft CMS
 *
 * PdfManager_PdfFiller Controller
 *
 * @author    Kenneth Huebsch
 * @link      https://puffin.dev
 * @package   PdfManager
 * @since     1.0.0
 */

namespace Craft;

abstract class PdfType
{
	const PatientEvaluation = 0;
	const VaccinationRecord = 1;
}

class PdfManager_PdfFillerController extends BaseController
{
		//data array used to fill fdf
		private $data;

		//where the new filled pdf will be saved
		private $output;

		//date of the appt
		private $date;

		//the name of the selectedPet
		private $selectedPet;

		//path of the fillable url
		private $pdfurl;

		//name of the file when it downloads
		private $filename;

		//returns a unique file name in craft's temp folder with the extension
		//passed in as an argument
		private function tmpfile($ext) {
			return tempnam(craft()->path->getTempPath(), '_pdfmanager'.$ext);
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
			$appointmentIdNumber = craft()->request->getRequiredParam('appointmentIdNumber');
			$this->selectedPet = craft()->request->getRequiredParam('selectedPet');
			$criteria = craft()->elements->getCriteria(ElementType::Entry);
			$criteria->appointmentIdNumber = $appointmentIdNumber;
			$entry = $criteria->first();
			if($entry != NULL)
			{
				$pet = $entry->pets[0];
				//locate the selected pet
				foreach($entry->pets as $block)
				{
					if($block->petFirstName == $this->selectedPet)
					{
						$pet = $block;
						break;
					}
				}

				if($pet != NULL)
				{
					$startTime = new DateTime($entry->appointmentStartTime);
					$this->date = new DateTime(DateTimeHelper::nice($entry->appointmentDate));
					$dob = new DateTime($pet->birthdate);
					$customer = craft()->users->getUserById($entry->customerRealId);
					if($pdfType == PdfType::PatientEvaluation)
					{
						$this->pdfurl = craft()->path->getPluginsPath().'pdfmanager/resources/patient-evaluation.pdf';
						$this->filename = $this->selectedPet.'_'.$customer['lastName'].'_Eval_'.$this->date->format('d-M-Y');
						$this->data = [
							    'Appt  ID'=> $entry->appointmentIdNumber,
							    'Date'=> $this->date->format('n/j/Y'),
							    'Time'=> $startTime->format('H:ia'),
							    'Customer ID' => $entry->customerIdNumber,
							    'Owner Last  Name' => $customer['lastName'],
							    'Vet  Name' => $entry->assignedVeterinarianName,
							    'Pet  Name' => $pet->petFirstName,
							    'Species' => $pet->species,
							    'Breed' => $pet->breed,
							    'SpayedNeutered YN' => $pet->spayedOrNeutered,
							    'Sex MF' => $pet->gender,
							    'AgeDOB' => $dob->format('n/j/Y'),
						];
					}
					elseif($pdfType == PdfTYpe::VaccinationRecord)
					{
						$this->pdfurl = craft()->path->getPluginsPath().'pdfmanager/resources/vaccination-record.pdf';
						$this->filename = $this->selectedPet.'_'.$customer['lastName'].'_VaxRecord_'.$this->date->format('d-M-Y');
						$vet = craft()->users->getUserById(substr($entry->assignedVeterinarianIdNumber, 3));
						$this->data = [
							    'Owner'=> $entry->customerName,
							    'Address 1'=> $entry->customerStreetAddress1,
							    'Address 2'=> $entry->customerCity.', '.$entry->customerstate.' '.$entry->customerZipCode,
							    'Pet  Name' => $pet->petFirstName,
							    'Species' => $pet->species,
							    'Breed' => $pet->breed,
							    'Sex' => $pet->gender,
							    'Age' => $dob->format('n/j/Y'),
							    'Date'=> $this->date->format('n/j/Y'),
							    'License'=> $vet->licenseNumber,
						];
					}
					else
					{
						//unknown pdf type
						die();
					}
				}
				else
				{
					//if you can't find the pet, return without downloading anything
					die();
				}
			}
			else
			{
				//if you can't find the entry, return without downloading anything
				die();
			}
		}

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
