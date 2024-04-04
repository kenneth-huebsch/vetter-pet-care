<?php
/*  Create unique Appointment ID for Appointment Record  */

namespace Craft;

class ApptIdPlugin extends BasePlugin{

    public function getName()
    {
        return 'Appointment ID';
    }

    public function getVersion()
    {
        return '1.0.0';
    }

    public function getDeveloper(){
        return 'The Tactile Group';
    }

    public function getDeveloperUrl()
    {
        return 'https://www.thetactilegroup.com';
    }

    protected function defineSetting(){

    }

    public function getSettingsHtml()
    {
        return craft()->templates->render('apptId/settings',array(
            'settings'=>$this->getSettings()
        ));
    }


    public function hasCpSection()
    {
        return false;
    }


    public function init()
    {
        parent::init();
    }

}