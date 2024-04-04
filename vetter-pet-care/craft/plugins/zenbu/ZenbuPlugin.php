<?php
/**
 * =======
 *  Zenbu
 * =======
 * See more data in your control panel entry listing
 * @copyright   Nicolas Bottari - Zenbu Studio 2011-2017
 * @author      Nicolas Bottari - Zenbu Studio
 * ------------------------------
 *
 * *** IMPORTANT NOTES ***
 * I (Nicolas Bottari and Zenbu Studio) am not responsible for any
 * damage, data loss, etc caused directly or indirectly by the use of this add-on.
 * @license     See the license documentation (text file) included with the add-on.
 *
 * Description
 * -----------
 * Zenbu is a powerful and customizable entry list manager.
 * Zenbu enables you to see, all on the same page:
 * Entry ID, Entry title, Entry date, Author name, all (or a portion of)
 * custom fields for the entry, etc.
 *
 * @link    http://zenbustudio.com/software/zenbu_craftcms/
 * @link    http://zenbustudio.com/software/docs/zenbu-craftcms/
 *
 */

namespace Craft;
require_once __DIR__.'/vendor/autoload.php';

use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\View;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Session;
use Zenbu\librairies\Settings;
use Zenbu\librairies\Fields;
use Zenbu\librairies\Sections;

class ZenbuPlugin extends BasePlugin
{
    public $fields;
    public $fieldsObj;
    public $sectionsObj;
    public $sections;
    public $all_fields;
    public $default_fields;
    public $settingsObj;

    public function __construct()
    {
        $this->sectionsObj                 = new Sections();
        $this->sections = $this->sectionsObj->getSections();
        $this->sectionsObj->setSections($this->sections);

        $this->fieldsObj                = new Fields();
        $this->fieldsObj->setSections($this->sections);
        $this->all_fields               = $this->fieldsObj->getFields();
        $this->default_fields           = $this->fieldsObj->getFields('default');
        $this->fieldsObj->setAllFields($this->all_fields);

        $this->settingsObj              = new Settings();
        $this->settingsObj->setAllFields($this->all_fields);
    }

    public function getName()
    {
        return Craft::t('Zenbu');
    }

    public function getDescription()
    {
        return 'Powerful and customizable entry list manager';
    }

    public function getVersion()
    {
        return '1.3.2';
    }

    public function getSchemaVersion()
    {
        return $this->getVersion();
    }

    public function getDeveloper()
    {
        return 'Zenbu Studio';
    }

    public function getDeveloperUrl()
    {
        return 'https://zenbustudio.com/software/zenbu-craftcms';
    }

    public function getDocumentationUrl()
    {
        return 'https://zenbustudio.com/software/docs/zenbu-craftcms';
    }

    public function getReleaseFeedUrl()
    {
        return 'https://zenbustudio.com/software/releasefeed/zenbu-craftcms';
    }

    public function hasCpSection()
    {
        return true;
    }

    protected function defineSettings()
    {
        $settings_array = array(
            'nav_label'                    => AttributeType::String,
            'redirect_to_zenbu'            => AttributeType::Bool,
            'hide_entries_tab'             => AttributeType::Bool,
            'show_search_criteria'         => AttributeType::Bool,
            'disable_entry_result_caching' => AttributeType::Bool,
            );

        return $settings_array;
    }

    public function getSettingsHtml()
    {
        $settings = craft()->plugins->getPlugin('zenbu')->getSettings();

        $data['nav_label']            = isset($settings['nav_label']) && ! empty($settings['nav_label']) ? $settings['nav_label'] : 'Zenbu';
        $data['redirect_to_zenbu']    = isset($settings['redirect_to_zenbu']) && ! empty($settings['redirect_to_zenbu']) ? TRUE : FALSE;
        $data['hide_entries_tab']     = isset($settings['hide_entries_tab']) && ! empty($settings['hide_entries_tab']) ? TRUE : FALSE;
        $data['show_search_criteria'] = isset($settings['show_search_criteria']) && ! empty($settings['show_search_criteria']) ? TRUE : FALSE;
        $data['disable_entry_result_caching'] = isset($settings['disable_entry_result_caching']) && ! empty($settings['disable_entry_result_caching']) ? TRUE : FALSE;

        return craft()->templates->render('zenbu/plugin_settings/index', array(
            'settings'                 => $data,
        ));
    }

    /**
     * Init function
     * Gets called on every request, before any events have had a chance to fire.
     * @return void
     */
    public function init()
    {
        if (craft()->request->isCpRequest()) {
            $settings     = craft()->plugins->getPlugin('zenbu')->getSettings();

            //    ----------------------------------------
            //    Redirect to Zenbu
            //    ----------------------------------------
            if (isset($settings['redirect_to_zenbu']) && ! empty($settings['redirect_to_zenbu'])) {
                craft()->on('entries.saveEntry', function ($event) use ($settings) {

                    //  Caches to keep
                    $zenbu_filter_cache = Cache::get('zenbu_filter_cache_'.Session::user()->id);

                    // Flush all caches after saving
                    craft()->cache->flush();

                    // Rebuild caches to keep
                    Cache::set('zenbu_filter_cache_'.Session::user()->id, $zenbu_filter_cache, 600);

                    $redirect_uris = explode('/', craft()->request->getPost('redirect'));

                    // If the URI has 2 segments, that means "Save & Leave" was selected,
                    // therefore redirect to Zenbu. If there are more than 2 segments,
                    // "Save and continue" and similar options were selected, so don't
                    // redirect to Zenbu.
                    if (count($redirect_uris) <= 2 && craft()->request->getPost('redirect') != 'settings/sections') {
                        $entryId     = $event->params['entry']->id;
                        $sectionId   = '?sectionId='.$event->params['entry']->sectionId;
                        $entryTypeId = isset($event->params['entry']->typeId) && ! empty($event->params['entry']->typeId) ? '&entryTypeId='.$event->params['entry']->typeId : '';
                        craft()->request->redirect(UrlHelper::getCpUrl('zenbu/'.$sectionId.$entryTypeId));
                    }
                });
            }

            //    ----------------------------------------
            //    Clear cache on field layout change,
            //    eg. Adding/removing/moving a field
            //    to/within a section
            //    ----------------------------------------
            craft()->on('fields.onSaveFieldLayout', function ($layout) {
                Cache::delete('getFields');
            });

            //    ----------------------------------------
            //    Clear cache on user save
            //    ----------------------------------------
            craft()->on('users.onSaveUser', function ($layout) {
                Cache::delete('getAuthors');
            });

            craft()->on('users.onDeleteUser', function ($layout) {
                Cache::delete('getAuthors');
            });
        }
    } // END init()

    // --------------------------------------------------------------------


    /**
     * Hook: Gives plugins a chance to register their own CP routes.
     * @return array An array of CP routes
     */
    public function registerCpRoutes()
    {
        return array(
            'zenbu'                       => array('action' => 'zenbu/index'),
            'zenbu/saveSidebarState'      => array('action' => 'zenbu/SaveSidebarState'),
            'zenbu/settings'              => array('action' => 'zenbu/displaySettings/Index'),
            'zenbu/settings/save'         => array('action' => 'zenbu/displaySettings/Save'),
            'zenbu/searches'              => array('action' => 'zenbu/savedSearches/Manage'),
            'zenbu/searches/manage'       => array('action' => 'zenbu/savedSearches/Manage'),
            'zenbu/searches/update'       => array('action' => 'zenbu/savedSearches/Update'),
            'zenbu/searches/save'         => array('action' => 'zenbu/savedSearches/Save'),
            'zenbu/searches/fetchFilters' => array('action' => 'zenbu/savedSearches/FetchFilters'),
        );
    } // END registerCpRoutes()

    // --------------------------------------------------------------------


    /**
     * Hook: Gives plugins a chance to modify the Control Panel navigation.
     * @param  array &$nav The CP Nav array
     * @return void
     */
    public function modifyCpNav(&$nav)
    {
        $settings          = craft()->plugins->getPlugin('zenbu')->getSettings();

        //    ----------------------------------------
        //    Change the Nav's "Zenbu" label to
        //    the user-set value, if present
        //    ----------------------------------------
        $data['nav_label'] = isset($settings['nav_label']) && ! empty($settings['nav_label']) ? $settings['nav_label'] : 'Zenbu';
        $nav['zenbu']['label'] = $data['nav_label'];

        //    ----------------------------------------
        //    Hide Entries Tab
        //    ----------------------------------------
        if (isset($settings['hide_entries_tab']) && ! empty($settings['hide_entries_tab'])) {
            unset($nav['entries']);
        }
    } // END modifyCpNav()

    // --------------------------------------------------------------------


    /**
     * Hook: Gives plugins a chance to register new user permissions.
     * @return array Additional user permissions
     */
    public function registerUserPermissions()
    {
        if (craft()->request->segments[0] == 'settings' && craft()->request->segments[1] == 'users' && craft()->request->segments[2] == 'groups') {
            return array(
                //'canAccessDisplaySettings' => array('label' => Craft::t('Can access display settings')),
                'canCopyZenbuDisplaySettingsToUsers' => array('label' => Craft::t('Can copy Display Settings to other users')),
            );
        }
    } // END registerUserPermissions()

    // --------------------------------------------------------------------


    /**
     * Hook: Gives plugins a chance to add a new Twig extension.
     * @return object New Twig Extension
     */
    public function addTwigExtension()
    {
        Craft::import('plugins.zenbu.twigextensions.ZenbuTwigExtension');

        return new ZenbuTwigExtension();
    } // END addTwigExtension()

    // --------------------------------------------------------------------

    /**
     * Gives plugins a chance to make additional table columns available to entry indexes.
     * @return string The aprsed field data, based on Zenbu's display settings
     */
    public function defineAdditionalEntryTableAttributes()
    {
        $fields = craft()->fields->getAllFields();

        $output = array();

        $output['status'] = Craft::t('Status');

        foreach($fields as $field)
        {
            if(is_numeric($field->id))
            {
                //    ----------------------------------------
                //    Make a list of Sections associated with
                //    fields that we're adding to the native listing
                //    ----------------------------------------
                $sections = $this->fieldsObj->getSectionsFromField($field->id);
                $sectionList = array();
                if(is_array($sections))
                {
                    foreach($sections as $section)
                    {
                        $sectionList[$section['id']] = $section['name'];
                    }
                }

                $sectionList = empty($sectionList) ? '' : ' (' /*. Lang::t('Used in: ')*/ . implode(', ', $sectionList) . ')';

                //    ----------------------------------------
                //    Label in native entry listing header or
                //    label in field ordering modal?
                //    ----------------------------------------
                $useInModal = Request::param('source') ? FALSE : TRUE;
                $sectionList = $useInModal === FALSE ? '' : $sectionList;

                $output['field:'.$field->id] = $field->name . $sectionList;
            }
        }

        return $output;
    } // END defineAdditionalEntryTableAttributes

    // --------------------------------------------------------------------


    /**
     * Gives plugins a chance to customize the HTML of the table cells on the entry index page.
     * @param  EntryModel $entry
     * @param  string     $attribute The field's attribute
     * @return string                The parsed field data, based on Zenbu's display settings
     */
    public function getEntryTableAttributeHtml(EntryModel $entry, $attribute)
    {
        $_POST['sectionId'] = $entry->section->id;

        $all_display_settings = $this->settingsObj->getDisplaySettings('all', $entry->sectionId);
        $display_settings     = $this->settingsObj->getDisplaySettings(NULL, $entry->sectionId);

        Cache::set('selectedNativeSection', $entry->section->id, 60);

        if(! Session::getFlash('zenbuModalWindowJsLoaded'))
        {
            craft()->templates->includeJs('
                    /**
                     * Modal window
                     */
                    $("body").delegate(".showModal", "click", function(e){
                        e.preventDefault();
                        var $div = $(this).next("div.outerModal").html();
                        var myModal = new Garnish.Modal($div, {
                            resizable: true,
                            onHide: function(){
                                myModal.destroy();
                                $(".modal-shade").remove();
                            }
                        });
                    });');
        }
        Session::setFlash('zenbuModalWindowJsLoaded', TRUE);

        //    ----------------------------------------
        //    Standard entry attributes
        //    ----------------------------------------
        if(in_array($attribute, array('id', 'section', 'author', 'uri', 'status', 'expiryDate', 'postDate')))
        {
            foreach($all_display_settings['fields'] as $key => $all_display_setting)
            {
                if($attribute == $all_display_setting['fieldType'] && $all_display_setting['showInNative'] == 1)
                {
                    $vars['settings'] = $display_settings;
                    $vars['entry']    = craft()->entries->getEntryById($entry->id);
                    $vars['col']      = array('handle' => $attribute);

                    //    ----------------------------------------
                    //    Custom override
                    //    ----------------------------------------
                    if(isset($display_settings['fields'][$key]['customDisplayStringTemplate']) && $display_settings['fields'][$key]['customDisplayStringTemplate'] !== '' )
                    {
                        return craft()->templates->renderString($display_settings['fields'][$key]['customDisplayStringTemplate'], $vars);
                    }

                    return craft()->templates->render('zenbu/columns/standard/'.$attribute, $vars, TRUE);
                }
            }

        }

        //    ----------------------------------------
        //    Custom fields
        //    ----------------------------------------
        if (strncmp($attribute, 'field:', 6) === 0)
        {
            $fieldId              = substr($attribute, 6);

            foreach($all_display_settings['fields'] as $key => $all_display_setting)
            {
                if($fieldId == $all_display_setting['fieldId'] && $all_display_setting['showInNative'] == 1)
                {
                    $field = craft()->fields->getFieldById($fieldId);

                    $vars['settings'] = $display_settings;
                    $vars['entry']    = craft()->entries->getEntryById($entry->id);
                    $vars['col']      = array('handle' => $field->handle, 'id' => $field->id);

                    //    ----------------------------------------
                    //    Custom override
                    //    ----------------------------------------
                    if(isset($display_settings['fields'][$key]['customDisplayStringTemplate']) && $display_settings['fields'][$key]['customDisplayStringTemplate'] !== '' )
                    {
                        return craft()->templates->renderString($display_settings['fields'][$key]['customDisplayStringTemplate'], $vars);
                    }

                    if(craft()->templates->doesTemplateExist('zenbu/columns/fieldtypes/'.strtolower($field->type)))
                    {
                        return craft()->templates->render('zenbu/columns/fieldtypes/'.strtolower($field->type), $vars, TRUE);
                    }
                    else
                    {
                        // Fallback if Zenbu field view doesn't exist
                        return craft()->templates->render('zenbu/columns/fieldtypes/defaultText', $vars, TRUE);
                    }
                }
            }
        }

    } // END getEntryTableAttributeHtml

    // --------------------------------------------------------------------

}
