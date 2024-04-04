<?php
/**
 * Notification plugin for Craft CMS
 *
 * This plugin will send email notification to user who sign up
 *
 * @author    Angela Chan
 * @copyright Copyright (c) 2017 Angela Chan
 * @link
 * @package   Notification
 * @since     1.0.0
 */

namespace Craft;

class NotificationPlugin extends BasePlugin
{
    function getName()
    {
        return Craft::t('Notification');
    }

    function getVersion()
    {
        return '1.0';
    }

    function getDeveloper()
    {
        return 'The Tactile Group';
    }

    function getDeveloperUrl()
    {
        return 'http://www.thetactilegroup.com';
    }



}