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

class DisplacedAppointmentsPlugin extends BasePlugin
{
    /**
     * @return mixed
     */
    public function init()
    {
        parent::init();
        craft()->templates->hook('displacedappointments.prepCpTemplate', [$this, 'prepCpTemplate']);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
         return Craft::t('Cancellations');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Allow admin to either cancel appointments without a vet or reassign appointments to vets with availability');
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
        return true;
    }

    /**
     * @return mixed
     */
    public function addTwigExtension()
    {
        Craft::import('plugins.displacedappointments.twigextensions.DisplacedAppointmentsTwigExtension');

        return new DisplacedAppointmentsTwigExtension();
    }

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

    /**
     * @return mixed
     */
    public function getSettingsHtml()
    {
       return craft()->templates->render('displacedappointments/DisplacedAppointments_Settings', array(
           'settings' => $this->getSettings()
       ));
    }
        public function registerCpRoutes()
    {
        return [

            'displacedappointments/displacedappointments/vetcancelled' => ['action' => 'displacedappointments/displacedappointments/vetter_cancelled'],
           
        ];
    }

      public function prepCpTemplate(&$context)
    {
        $user = craft()->userSession->getUser();
        $context['subnav']['index'] = ['label' => Craft::t('Dashboard'), 'url' => 'displacedappointments/index'];
        $context['subnav']['vetter_cancelled'] = ['label' => Craft::t('Vet/Admin Cancelled'), 'url' => 'displacedappointments/vetter_cancelled'];
        $context['subnav']['customer_cancelled'] = ['label' => Craft::t('Customer Cancelled'), 'url' => 'displacedappointments/customer_cancelled'];

         //below is stuff from charge
       /* if (craft()->charge_membershipSubscription->systemHasAnySubscriptions()) {
            $context['subnav']['subscribers'] = ['label' => Craft::t('Subscribers'), 'url' => 'charge/subscribers'];
        }

        if (craft()->charge_connect->getConnectEnabledStatus()) {
            $context['subnav']['connect'] = ['label' => Craft::t('Accounts'), 'url' => 'charge/connect'];
        }
        */
    }

    /**
     * @return mixed
     */
    public function prepSettings($settings)
    {
        // Modify $settings here...

        return $settings;
    }
}
