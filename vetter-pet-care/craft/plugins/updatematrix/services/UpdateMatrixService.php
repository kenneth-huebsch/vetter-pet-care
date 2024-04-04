<?php
	namespace Craft;
	
	
	/**
	* UpdateMatrixService
	*/
	class UpdateMatrixService extends BaseApplicationComponent
	{

        /**
         *  Delete Matrix
         */
        public function deleteMatrix($field_id, $asset_id)
		{

			/* delete in vetter.craft_matrixblocks, vetter.craft_matrixcontent_pets */
            craft()->matrix->deleteBlockById($field_id);
			
			/* update S3 assets */
			if ($asset_id != 0){
                craft()->assets->deleteFiles($asset_id);
            }
            /* send flash message */
            craft()->userSession->setFlash('DeleteCompleted', "Delete completed");

		}


	
	}
	