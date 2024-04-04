<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\frameworks\craft\Base as Base;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\Db;
use Zenbu\librairies\Fields;

class Settings extends Base
{
    var $fields;

    public function __construct()
    {
    }

    public function getBaseSettings()
    {
        return Craft\craft()->plugins->getPlugin('zenbu')->getSettings();
    }

	public function getDisplaySettings($what = '', $sId = 0, $etId = 0)
    {
        $sectionId    = Request::param('sectionId', $sId);
        $subSectionId = Request::param(
                            Convert::col('subSectionId'),
                            $sectionId == 0 ? 0 : Craft\craft()->sections->getSectionById($sectionId)->entryTypes[0]->id,
                            $etId
                            );

        // We should never run into this js-based situation
        // but adding this just in case.
        if($subSectionId == 'undefined')
        {
            $subSectionId = 0;
        }

        if($out = Cache::get('getDisplaySettings_w:'.$what.'_s'.$sectionId.'_e'.$subSectionId.'_u'.Session::user()->id))
        {
            return $out;
        }

    	$sql = '/* '.__FUNCTION__.' */ SELECT zds.* FROM zenbu_display_settings zds
		    	WHERE zds.sectionId = '. $sectionId . '
                AND zds.'.Convert::col("subSectionId").' IN (' . $subSectionId . ', 0)
		    	AND zds.userId = ' . Session::user()->id;

		if($what != 'all')
        {
            $sql .= " AND zds.show = 1";
        }

        $sql .= " ORDER BY `order` ASC";

        $results = Db::rawQuery($sql);

        $display_settings = array();

        if(count($results) > 0)
        {
            foreach($results as $row)
            {
				$display_settings['sectionId']                  = $row['sectionId'];
				$display_settings[Convert::col('subSectionId')] = $row[Convert::col('subSectionId')];
				$display_settings['fields'][$row['order']]      = array(
                    'show'                        => $row['show'],
                    'showInNative'                => $row['showInNative'],
                    'fieldType'                   => $row[Convert::col('fieldType')],
                    'fieldId'                     => $row[Convert::col('fieldId')],
                    'order'                       => $row['order'],
                    'settings'                    => json_decode($row['settings']),
                    'customDisplayStringTemplate' => $row['customDisplayStringTemplate'],
                    );
                $handle = $row[Convert::col('fieldType')] == 'field' ? $row[Convert::col('fieldId')] : $row[Convert::col('fieldType')];
                $display_settings['settings'][$handle]                    = json_decode($row['settings']);
                $display_settings['customDisplayStringTemplate'][$handle] = $row['customDisplayStringTemplate'];
            }
        }
        else
        {
            $display_settings = $this->getDefaultDisplaySettings();
        }

        Cache::set('getDisplaySettings_w:'.$what.'_s'.$sectionId.'_e'.$subSectionId.'_u'.Session::user()->id, $display_settings, 0);

        return $display_settings;

    } // END getDisplaySettings

    // --------------------------------------------------------------------

    public function getDefaultDisplaySettings()
    {
        $display_settings['sectionId']                   = $sectionId = Request::post('sectionId', 0);
        $display_settings['entryTypeId']                 = $subSectionId = Request::post('entryTypeId', 0);
        $display_settings['settings']                    = array();
        $display_settings['customDisplayStringTemplate'] = array();

        if($out = Cache::get('getDefaultDisplaySettings_s'.$sectionId.'_e'.$subSectionId.'_u'.Session::user()->id))
        {
            return $out;
        }

        $fields = $this->getAllFields();

        foreach($fields as $handle => $field)
        {
            $display_settings['fields'][] = array(
                'show'         => 1,
                'showInNative' => 0,
                'fieldType'    => is_integer($handle) ? 'field' : $handle,
                'fieldId'      => is_integer($handle) ? $handle : 0,
                'order'        => 0,
                'settings'     => ''
                );
        }

        Cache::set('getDefaultDisplaySettings_s'.$sectionId.'_e'.$subSectionId.'_u'.Session::user()->id, $display_settings, 0);

        return $display_settings;
    }
}