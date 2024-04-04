<?php
/**
 * Displaced Appointments plugin for Craft CMS
 *
 * Allow admin to either cancel appointments without a vet or reassign appointments to vets with availability
 *
 * @author    Matthew Cieslak
 * @copyright Copyright (c) 2017 Matthew Cieslak
 * @link      https://github.com/mattman93
 * @package   DisplacedAppointments
 * @since     1.0.0
 */

namespace Craft;

class DiscountsPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Discounts');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Checks discount code, applies discount to user records');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return '???';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return '???';
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getSchemaVersion()
    {
        return '1.0.0';
    }

    /**
     * @return string
     */
    public function getDeveloper()
    {
        return 'Matthew Cieslak';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://github.com/mattman93';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return false;
    }

    /**
     * @return mixed
     */

    /**
     */
    public function onBeforeInstall()
    {
    }

    /**
     */
    public function onAfterInstall()
    {
    }

    /**
     */
    public function onBeforeUninstall()
    {
    }

    /**
     */
    public function onAfterUninstall()
    {
    }

    /**
     * @return array
     */
    protected function defineSettings()
    {
        return array(
            'someSetting' => array(AttributeType::String, 'label' => 'Some Setting', 'default' => ''),
        );
    }


}
