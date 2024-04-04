<?php
namespace Craft;

use Zenbu\controllers\ZenbuBaseController as ZenbuBaseController;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\View;
use Zenbu\frameworks\craft\Request;

class ZenbuController extends ZenbuBaseController
{
	public function __construct()
	{
		parent::init();
		$message = $this->session->getFlash('messageNotice');
		craft()->userSession->setNotice($message);
	}

	/**
	 * Main page
	 * @return string Rendered template
	 */
	public function actionIndex()
	{
		if(craft()->request->getIsPostRequest() === FALSE)
		{
			$selectOptions                            = $this->sectionsObj->getSectionSelectOptions();
			$this->vars['title']                      = $this->nav_label;
			$this->vars['selectedZenbuSection']       = 'main';
			$this->vars['lastBreadcrumbLabel']        = Lang::t('Entry Listing');
			$this->vars['section_dropdown_options']   = $selectOptions['sections'];
			$this->vars['entryType_dropdown_options'] = $selectOptions['entryTypes'];
			$this->vars['limit_options']              = $this->filters->getLimitSelectOptions();
			$this->vars['orderby_options']            = $this->filters->getOrderBySelectOptions();
			$this->vars['sort_options']               = $this->filters->getSortSelectOptions();
			$this->vars['firstFilterOptions']         = $this->filters->getFirstFilterOptions();
			$this->vars['secondFilterOptions']        = $this->filters->getSecondFilterOptions();
			$this->vars['sections']                   = $this->sectionsObj->getSections();
			$this->vars['savedSearches']              = $this->saved_searches->getSavedSearches();
			$this->vars['authors']                    = $this->users->getAuthors();
			$this->vars['sidebar_hidden']             = Cache::get('zenbu_sidebar_hidden_'.$this->user->id);
		}

		// $this->vars['fields']            = $fields = $this->all_fields;
		$this->vars['fields']            = $fields = $this->fieldsObj->getFieldsPerSection();
		$this->vars['customFields']      = $this->fieldsObj->getCustomFields();
		$this->vars['categoryFieldData'] = $this->fieldsObj->getCategoryFieldData();
		$this->vars['savedSearch']       = $this->saved_searches->getSavedSearchFilters();
		$this->vars['storedFilterData']  = FALSE;

		//	----------------------------------------
		//	If this is not a saved search request, or
		//	there is no saved search to retrieve,
		//	try getting filters from DB cache, if
		//	there's anything available
		//	----------------------------------------
		if( empty($this->vars['savedSearch']) )
		{
			// Comment out the following line if constant cache retrieval is being bothersome
			$this->vars['storedFilterData'] = Cache::get('zenbu_filter_cache_'.$this->user->id);
		}
		$this->vars['settings']         = $this->display_settings;

		//	----------------------------------------
		//	Order "show" fields first
		//	----------------------------------------
		$orderedFields = $this->fieldsObj->getOrderedFields();

		$this->vars['columns'] = empty($orderedFields) ? $this->vars['fields'] : $orderedFields;

		if(craft()->request->getIsPostRequest() !== FALSE)
		{
			$this->filters->cacheFilters();
			$this->renderTemplate('zenbu/main/results', $this->vars);
		}
		else
		{
			if($this->vars['sidebar_hidden'])
			{
				//    ----------------------------------------
			    //    If this special <style> element is found,
			    //    eg. if the sidebar state pulled from cache
			    //    is closed and therefore load this element,
			    //    remove the styles on first interaction
			    //    with the sidebar toggle (target is
			    //    data-hide-sidebar-on-load="yes" attribute)
			    //    ----------------------------------------
				$sidebarHideStyle = '<style data-hide-sidebar-on-load="yes">.sidebar{display: none !important;}body.ltr .content.has-sidebar:not(.hiding-sidebar){margin-left: 0 !important;}</style>';
				craft()->templates->includeHeadHtml($sidebarHideStyle);
			}

			//	----------------------------------------
			//	We need Vue.js' data var before vue.js
			//	is loaded. Here we get our parsed data var
			//	and load it in the page's head.
			//	Vue.js will then be later loaded in the footer.
			//	----------------------------------------
			$headJs = $this->renderTemplate('zenbu/main/js/_variables.js', $this->vars, TRUE);
			craft()->templates->includeHeadHtml($headJs);

			if(craft()->config->get('devMode'))
			{
				$scripts = array(
					'zenbu/js/zenbu/zenbu.main.js',
					'zenbu/js/jquery-ui.min.js',
					'zenbu/js/lodash/lodash.min.js',
					'zenbu/js/vue2/vue.js',
					'zenbu/js/zenbu/zenbu.main.vue.js'
				);
			}
			else
			{
				$scripts = array(
					'zenbu/js/zenbu.main.min.js'
				);
			}

			View::includeJs($scripts);

			$this->renderTemplate('zenbu/main/index', $this->vars);
		}

	} // END actionIndex()

	// --------------------------------------------------------------------


    /**
     * Delete Entries
     * @return void Redirect
     */
    public function actionDeleteEntries()
    {
    	$elementIds        = craft()->request->getRequiredPost('elementIds');

    	if( $elementIds )
    	{
    		foreach($elementIds as $entryId)
    		{
    	 		$response = craft()->entries->deleteEntryById($entryId);
    		}
    	}

    	$this->session->setFlash('messageNotice', Lang::t("Entries Deleted"));

    	craft()->request->redirect(UrlHelper::getCpUrl('zenbu'));
    } // END actionDeleteEntries()

    // --------------------------------------------------------------------


    /**
     * Save the sidebar state in cache, open or closed
     * @return void Echoed JSON string
     */
    public function actionSaveSidebarState()
    {
		$state = Request::param('state', 'open');

		$sidebar_hidden = $state == 'open' ? FALSE : TRUE;

		Cache::set('zenbu_sidebar_hidden_'.$this->user->id, $sidebar_hidden, 0);

		echo json_encode(array('state' => $state, 'sidebar_hidden' => $sidebar_hidden));
    } // END actionSaveSidebarState()

    // --------------------------------------------------------------------

}