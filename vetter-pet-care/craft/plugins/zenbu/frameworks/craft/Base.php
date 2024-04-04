<?php
namespace Zenbu\frameworks\craft;

class Base
{
	var $display_settings;
	var $general_settings;
	var $permissions;
	var $session;
	var $settings;
	var $user;

	public function __construct()
	{
	}

	public function setFieldsObj($fields)
    {
        $this->fieldsObj = $fields;
    }

    public function getFieldsObj()
    {
        return $this->fieldsObj;
    }

    public function setAllFields($fields)
    {
        $this->allFields = $fields;
    }

    public function getAllFields()
    {
        return $this->allFields;
    }

    public function setFieldtypes($fields)
    {
        $this->fieldtypes = $fields;
    }

    public function getFieldtypes()
    {
        return $this->fieldtypes;
    }

    public function setFieldSettings($fields)
    {
        $this->fieldSettings = $fields;
    }

    public function getFieldSettings()
    {
        return $this->fieldSettings;
    }

    public function setDefaultFields($fields)
    {
        $this->default_fields = $fields;
    }

    public function getDefaultFields()
    {
        return $this->default_fields;
    }

    public function setDisplaySettings($settings)
    {
        $this->display_settings = $settings;
    }

    public function getDisplaySettings()
    {
        return $this->display_settings;
    }

    public function setGeneralSettings($settings)
    {
        $this->general_settings = $settings;
    }

    public function getGeneralSettings()
    {
        return $this->general_settings;
    }

    public function setPermissions($settings)
    {
        $this->permissions = $settings;
    }

    public function getPermissions()
    {
        return $this->permissions;
    }

    public function setSections($sections)
    {
        $this->sections = $sections;
    }

    public function getSections()
    {
        return $this->sections;
    }
}