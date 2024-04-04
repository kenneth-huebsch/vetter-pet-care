<?php
/**
 * Update Matrix plugin for Craft CMS
 *
 * This plugin will delete/update Matrix field entires
 *
 * 
 *
 * @author    Angela Chan
 * @copyright Copyright (c) 2017 Angela Chan
 * @link      
 * @package   UpdateMatrix
 * @since     1.0.0
 */

namespace Craft;

class UpdateMatrixPlugin extends BasePlugin
{
	function getName()
    {
         return Craft::t('Update Matrix');
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

    public function init()
    {
        parent::init();
        craft()->on('users.onDeleteUser', function (Event $event) {
            $user = $event->params['user'];
            //$userId = craft()->request->getPost('userId');
            //$user = craft()->users->getUserById($userId);

            /* delete S3 asset */
            Craft::log("delete S3 folder" . $user->username, LogLevel::Info, true);
        });
    }
	
	
	}