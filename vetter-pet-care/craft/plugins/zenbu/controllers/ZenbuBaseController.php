<?php
namespace Zenbu\controllers;

use Craft;
use Craft\BaseController as BaseController;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\View;
use Zenbu\librairies\Settings;
use Zenbu\librairies\Fields;
use Zenbu\librairies\Filters;
use Zenbu\librairies\Sections;
use Zenbu\librairies\SavedSearches;
use Zenbu\librairies\Users;

class ZenbuBaseController extends BaseController
{
	public $user;
	public $nav_label;
	public $sectionsObj;
	public $sections;
	public $fieldsObj;
	public $all_fields;
	public $default_fields;
	public $filters;
	public $session;
	public $settingsObj;
	public $settings;
	public $base_settings;
	public $display_settings;
	public $all_display_settings;
	public $saved_searches;
	public $vars;
	public $users;

	public function __construct()
	{
	}

	public function init()
	{
		View::includeCss(array('zenbu/css/zenbu.css'));

		$this->user                     = Session::user();
		$this->sectionsObj                 = new Sections();
		$this->sections = $this->sectionsObj->getSections();
		$this->sectionsObj->setSections($this->sections);

		$this->fieldsObj                = new Fields();
		$this->fieldsObj->setSections($this->sections);
		$this->all_fields               = $this->fieldsObj->getFields();
		$this->default_fields           = $this->fieldsObj->getFields('default');

		$this->filters                  = new Filters();
		$this->filters->setDefaultFields($this->default_fields);
		$this->filters->setAllFields($this->all_fields);
		$this->filters->setFieldsObj($this->fieldsObj);
		$this->filters->setSections($this->sections);

		$this->session                  = new Session();
		$this->settingsObj              = new Settings();
		$this->settingsObj->setAllFields($this->all_fields);
		$this->base_settings            = $this->settingsObj->getBaseSettings();
		$this->display_settings         = $this->settingsObj->getDisplaySettings();
		$this->all_display_settings     = $this->settingsObj->getDisplaySettings('all');
		$this->saved_searches           = new SavedSearches();
		$this->users                    = new Users();

		$this->fieldsObj->setDisplaySettings($this->display_settings);
		$this->fieldsObj->setAllFields($this->all_fields);

		$this->nav_label                = isset($this->base_settings['nav_label']) && ! empty($this->base_settings['nav_label']) ? $this->base_settings['nav_label'] : 'Zenbu';
		$this->vars['user']             = $this->user;
		$this->vars['navLabel']         = $this->nav_label;
		$this->vars['craftVersion']     = $craftVersion = Craft\craft()->getVersion();
		$this->vars['craftVersionFull'] = version_compare($craftVersion, '2.6.2951', '>=') ? $craftVersion : $craftVersion.'.'.Craft\craft()->getBuild();
		$this->vars['sectionId']        = $sectionId = Craft\craft()->request->getParam('sectionId');
		$this->vars['entryTypeId']      = $entryTypeId = Craft\craft()->request->getParam('entryTypeId');
		$this->vars['sectionIdParam']   = $sectionId ? '?sectionId=' . $sectionId : '';
		$this->vars['entryTypeIdParam'] = $entryTypeId ? '&entryTypeId=' . $entryTypeId : '';
		// Site's default locale
		$this->vars['baseLocale']       = Craft\craft()->getLocale()->id;
		// First locale of selected section.
		$this->vars['firstLocale']      = $sectionId ? Craft\craft()->sections->getSectionLocales($sectionId)[0]['locale'] : $this->vars['baseLocale'];
		$this->vars['baseSettings']     = $this->base_settings;
		$this->vars['devMode']          = Craft\craft()->config->get('devMode');


	}

	public function zenbuRenderTemplate($template = '', $content = array())
	{
		return $this->renderTemplate($template, $content, TRUE);
	}
}