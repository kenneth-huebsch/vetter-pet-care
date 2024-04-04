<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\frameworks\craft\Base as Base;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\Db;
use Zenbu\frameworks\craft\FieldsBase as FieldsBase;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Session;
use Zenbu\librairies;

class Fields extends Base
{
    var $fields;

    public function __construct()
    {
        parent::__construct();
    } // END __construct()

    // --------------------------------------------------------------------


    /**
     * Retrieve field data
     * @param  boolean $sectionId
     * @param  boolean $subSectionId Entry Type
     * @return array
     */
    public function getFields($sectionId = FALSE, $subSectionId = FALSE)
    {
        $sectionId    = $sectionId === FALSE ? Request::param(Convert::col('sectionId')) : $sectionId;
        $subSectionId = $subSectionId === FALSE ? Request::param(Convert::col('subSectionId')) : $subSectionId;

        $this->fieldsBase = new FieldsBase();
        $this->std_fields = $this->fieldsBase->std_fields;

        if($sectionId == 'default')
        {
            return $this->std_fields;
        }

        $this->fieldsBase->setSections($this->getSections());
        $this->fields = $this->fieldsBase->getFields();

        return $this->fields;
    } // END getFields()

    // --------------------------------------------------------------------

    public function getFieldsPerSection($sectionId = FALSE, $subSectionId = FALSE)
    {
        $sectionId    = $sectionId === FALSE ? Request::param(Convert::col('sectionId')) : $sectionId;
        $subSectionId = $subSectionId === FALSE ? Request::param(Convert::col('subSectionId')) : $subSectionId;

        $this->fields = $this->getFields();

        if(isset($this->fields[Convert::col('sectionId').'_'.$sectionId]))
        {
            if(! $subSectionId)
            {
                foreach($this->fields[Convert::col('sectionId').'_'.$sectionId] as $etId => $array)
                {
                    $subSectionId = str_replace(Convert::col('subSectionId').'_', '', $etId);
                }
            }

            return $this->fields[Convert::col('sectionId').'_'.$sectionId][Convert::col('subSectionId').'_'.$subSectionId];
        }
        else
        {
            return $this->std_fields;
        }
    }


    /**
     * Get a list of ordered fields
     * @param  boolean $include_nonvisible Include fields set as not shows
     * @return array                      The list of fields
     */
    public function getOrderedFields($include_nonvisible = FALSE)
    {
        $settings = $this->getDisplaySettings();
        $fields   = $this->getFieldsPerSection();

        $vars = array();

        foreach($settings['fields'] as $key => $setting)
        {
            $fieldkey = $setting[Convert::col('fieldType')] == 'field' ? $setting[Convert::col('fieldId')] : $setting[Convert::col('fieldType')];
            if(isset($fields[$fieldkey]))
            {
                $vars[$fieldkey] = $fields[$fieldkey];
            }
        }

        if($include_nonvisible !== FALSE)
        {
            foreach($fields as $fieldkey => $field_data)
            {
                if( ! isset($vars[$fieldkey]) )
                {
                    $vars[$fieldkey] = $field_data;
                }
            }
        }

        return $vars;
    } // END getOrderedFields

    // --------------------------------------------------------------------


    /**
     * Find Section info based on field ID
     * @param  int $field_id
     * @return array           section data array
     */
    public function getSectionsFromField($field_id)
    {
        $this->fieldsBase->setSections($this->getSections());
        return $this->fieldsBase->getSectionsFromField($field_id);
    } // END getSectionsFromField()

    // --------------------------------------------------------------------

    /**
     * Get a list of custom fields
     * @return array
     */
    public function getCustomFields()
    {
        $this->fieldsBase->setAllfields($this->getAllFields());
        return $this->fieldsBase->getCustomFields();
    } // END getCustomFields()

    // --------------------------------------------------------------------


    public function getCategoryFieldData()
    {
        return $this->fieldsBase->getCategoryFieldData();
    } // END getCustomFields()

    // --------------------------------------------------------------------
}