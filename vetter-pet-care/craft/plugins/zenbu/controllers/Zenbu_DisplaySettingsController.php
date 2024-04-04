<?php
namespace Craft;

use Zenbu\controllers\ZenbuBaseController as ZenbuBaseController;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\View;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Convert;

class Zenbu_DisplaySettingsController extends ZenbuBaseController
{
	public function __construct()
	{
		parent::init();
		$message = $this->session->getFlash('messageNotice');
		craft()->userSession->setNotice($message);
	}


	/**
	 * Display Settings
	 * @return string Rendered template
	 */
    public function actionIndex()
    {
		$this->vars['settings'] = $this->all_display_settings;

		//	----------------------------------------
		//	Get rid of Zenbu filter cache
		//	This avoids sectionId/entryTypeId mismatch if
		//	returning to main Zenbu page
		//	----------------------------------------
		Cache::delete('zenbu_filter_cache_'.$this->user->id);

		//	----------------------------------------
		//	Order "show" fields first
		//	----------------------------------------
		$this->vars['rows'] = $this->fieldsObj->getOrderedFields(TRUE);

		if(craft()->request->getIsPostRequest() !== FALSE)
		{

			$this->renderTemplate('zenbu/display_settings/settings', $this->vars);
		}
		else
		{
			$this->vars['title']                      = $this->vars['lastBreadcrumbLabel'] = Lang::t('Display Settings');
			$this->vars['selectedZenbuSection']       = 'displaySettings';
			$this->vars['nav_label']                  = $this->nav_label;
			$this->vars['userGroups']                 = ! empty(craft()->userGroups) ? craft()->userGroups->getAllGroups() : array();
			$this->vars['sections']                   = $this->sectionsObj->getSections();
			$selectOptions                            = $this->sectionsObj->getSectionSelectOptions();
			$this->vars['section_dropdown_options']   = $selectOptions['sections'];
			$this->vars['entryType_dropdown_options'] = $selectOptions['entryTypes'];

			//  ----------------------------------------
	        //  We need Vue.js' data var before vue.js
	        //  is loaded. Here we get our parsed data var
	        //  and load it in the page's head.
	        //  Vue.js will then be later loaded in the footer.
	        //  ----------------------------------------
	        $headJs = $this->renderTemplate('zenbu/display_settings/js/_variables.js', $this->vars, TRUE);
	        craft()->templates->includeHeadHtml($headJs);

			View::includeJs(array(
				'zenbu/js/zenbu.display_settings.min.js',
				// 'zenbu/js/zenbu.display_settings.js',
				// 'zenbu/js/jquery-ui.min.js',
				// 'zenbu/js/lodash/lodash.min.js',
				// 'zenbu/js/vue2/vue.min.js',
				// 'zenbu/js/zenbu.display_settings.vue.js',
				));
			$this->renderTemplate('zenbu/display_settings/index', $this->vars);
		}
    } // END actionIndex()

    // --------------------------------------------------------------------


    public function actionSave()
    {
		$fields                      = Request::post('field');
		$fieldsettings               = Request::post('fieldsettings');
		$showInNative                = Request::post('native');
		$customDisplayStringTemplate = Request::post('customDisplayStringTemplate');
		$applyTo                     = Request::post('applyTo');

		$sectionId     = Request::param('sectionId', 0);
		$entryTypeId   = Request::param('entryTypeId', $sectionId == 0 ? 0 : craft()->sections->getSectionById($sectionId)->entryTypes[0]->id);
		$c             = 1;

		$display_settings = new Zenbu_DisplaySettingsRecord;
    	$display_settings->deleteAll('(sectionId IS NULL OR sectionId = ?) AND (entryTypeId IS NULL OR entryTypeId = ?) AND userId = ?', array(
    			$sectionId,
    			$entryTypeId,
    			$this->user->id
			)
		);

		$users = array();

		//	----------------------------------------
		//	Find individual users based on selected
		//	user groups or "all" users, then
		//	1. Delete old records
		//	2. Insert new records
		//	----------------------------------------

		//	----------------------------------------
		//	...when apply to all is NOT checked
		//	----------------------------------------
    	if(isset($applyTo['all']) && $applyTo['all'] != 1)
    	{
	    	foreach($applyTo as $key => $groupId)
	    	{
	    		if($key != 'all')
	    		{
					$criteria          = craft()->elements->getCriteria(ElementType::User);
					$criteria->groupId = $groupId;
					$criteria->limit   = NULL;
					$users[$groupId]   = $criteria->find();

	    			foreach($users as $groupId => $user_list)
	    			{
	    				foreach($user_list as $user)
	    				{
		    				if($user['id'] != $this->user->id)
	    					{
								$ds_delete = new Zenbu_DisplaySettingsRecord;
						    	$ds_delete->deleteAll('(sectionId IS NULL OR sectionId = ?) AND (entryTypeId IS NULL OR entryTypeId = ?) AND userId = ?', array(
						    			$sectionId,
						    			$entryTypeId,
						    			$user['id']
									)
								);
								unset($ds_delete);
				    		}
				    	}
	    			}
	    		}
	    	}
	    }
		//	----------------------------------------
		//	...when apply to all is checked
		//	----------------------------------------
	    elseif(isset($applyTo['all']) && $applyTo['all'] == 1)
	    {
			$criteria        = craft()->elements->getCriteria(ElementType::User);
			$criteria->limit = NULL;
			$users[]         = $criteria->find();

	        foreach($users as $groupId => $user_list)
    		{
    			foreach($user_list as $user)
    			{
	    			if($user['id'] != $this->user->id)
	    			{
						$ds_delete = new Zenbu_DisplaySettingsRecord;
				    	$ds_delete->deleteAll('(sectionId IS NULL OR sectionId = ?) AND (entryTypeId IS NULL OR entryTypeId = ?) AND userId = ?', array(
				    			$sectionId,
				    			$entryTypeId,
				    			$user['id']
							)
						);
						unset($ds_delete);
		    		}
		    	}
    		}
    	}

		if( ! $fields )
		{
			Request::redirect(UrlHelper::getCpUrl('zenbu/settings'));
		}

    	foreach($fields as $key => $setting)
    	{
    		foreach($setting as $handle => $show)
    		{
				$display_settings                              = new Zenbu_DisplaySettingsRecord;
				$display_settings->userId                      = $this->user->id;
				$display_settings->userGroupId                 = 0;
				$display_settings->sectionId                   = $sectionId;
				$display_settings->entryTypeId                 = $entryTypeId;
				$display_settings->fieldType                   = is_integer($handle) ? 'field' : $handle;
				$display_settings->fieldId                     = is_integer($handle) ? $handle : 0;
				$display_settings->order                       = $c;
				$display_settings->show                        = empty($show) ? FALSE : TRUE;
				$display_settings->showInNative                = isset($showInNative[$key][$handle]) && $showInNative[$key][$handle] == 1 ? TRUE : FALSE;
				$display_settings->settings                    = isset($fieldsettings[$handle]) ? $fieldsettings[$handle] : NULL;
				$display_settings->customDisplayStringTemplate = isset($customDisplayStringTemplate[$handle]) ? $customDisplayStringTemplate[$handle] : '';
		    	$display_settings->save();
		    	unset($display_settings);

		    	//	----------------------------------------
		    	//	Copy to users, which were found based on
		    	//	selected user group or if "all" users
		    	//	was selected.
		    	//	----------------------------------------
    			foreach($users as $groupId => $user_list)
    			{
    				foreach($user_list as $user)
    				{
	    				if($user['id'] != $this->user->id)
						{
							$display_settings                              = new Zenbu_DisplaySettingsRecord;
							$display_settings->userId                      = $user['id'];
							$display_settings->userGroupId                 = 0;
							$display_settings->sectionId                   = $sectionId;
							$display_settings->entryTypeId                 = $entryTypeId;
							$display_settings->fieldType                   = is_integer($handle) ? 'field' : $handle;
							$display_settings->fieldId                     = is_integer($handle) ? $handle : 0;
							$display_settings->order                       = $c;
							$display_settings->show                        = empty($show) ? FALSE : TRUE;
							$display_settings->settings                    = isset($fieldsettings[$handle]) ? $fieldsettings[$handle] : NULL;
							$display_settings->customDisplayStringTemplate = isset($customDisplayStringTemplate[$handle]) ? $customDisplayStringTemplate[$handle] : '';
			    			$display_settings->save();
			    			unset($display_settings);

			    			//	----------------------------------------
							//	Clear the Display Settings cache
							//	----------------------------------------
							Cache::delete('getDisplaySettings_w:all_s'.$sectionId.'_e'.$entryTypeId.'_u'.$user['id']);
							Cache::delete('getDisplaySettings_w:_s'.$sectionId.'_e'.$entryTypeId.'_u'.$user['id']);
			    		}
			    	}
    			}

	    		$c++;
    		}
    	}

    	//	----------------------------------------
    	//	Clear template-level entry caching.
    	//	----------------------------------------
    	$criteria = craft()->elements->getCriteria(ElementType::Entry);
		$criteria->sectionId = $sectionId;
		$criteria->limit = NULL;
		$affectedEntries = $criteria->find();

		foreach($affectedEntries as $entry)
		{
			// Clear for all locales
			foreach($entry->getLocales() as $locale => $array)
			{
		    	craft()->templateCache->deleteCachesByKey('zenbu_e'.$entry->id.'_s'.$sectionId.'_u'.$this->user->id.'_l'.$locale);
			}

			// Clear for other users, if any
			if(! empty($users))
			{
				foreach($users as $groupId => $user_list)
	    		{
					foreach($user_list as $user)
					{
						if($user['id'] != $this->user->id)
						{
							// Clear for all locales
							foreach($entry->getLocales() as $locale => $array)
							{
						    	craft()->templateCache->deleteCachesByKey('zenbu_e'.$entry->id.'_s'.$sectionId.'_u'.$user['id'].'_l'.$locale);
							}
						}
					}
				}
			}
		}

		//	----------------------------------------
		//	Clear the Display Settings cache
		//	----------------------------------------
		Cache::delete('getDisplaySettings_w:all_s'.$sectionId.'_e'.$entryTypeId.'_u'.$this->user->id);
		Cache::delete('getDisplaySettings_w:_s'.$sectionId.'_e'.$entryTypeId.'_u'.$this->user->id);

		$this->session->setFlash('messageNotice', Lang::t("Display Settings Saved"));

    	Request::redirect(UrlHelper::getCpUrl('zenbu/settings?sectionId='.$sectionId.'&entryTypeId='.$entryTypeId));
    } // END actionSave()

    // --------------------------------------------------------------------

}