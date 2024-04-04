<?php
namespace Craft;

/**
* UpdateMatrix_MatrixController
*/
class UpdateMatrixController extends BaseController {
	protected $allowAnonymous = true;
	

	public function actionDelete_pet(){
	
		$this->requireAjaxRequest();
        $field_id = craft()->request->getRequiredPost('fieldId');
        $asset_id = craft()->request->getRequiredPost('assetId');

        /* delete Matrix and Asset in S3*/
		craft()->updateMatrix->deleteMatrix($field_id, $asset_id);
        //craft()->notification->sendMessage();

        $log_message = "test from calling";
        UpdateMatrixPlugin::log($log_message,LogLevel::Info,true);


        /* to avoid jquery 404 error */
        die();
    }


}
	