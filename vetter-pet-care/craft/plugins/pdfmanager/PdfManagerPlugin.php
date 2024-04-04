<?php
/**
 * PdfManager plugin for Craft CMS
 *
 * Populate editable fields in a pdf form with craft data.
 *
 * @author    Kenneth Huebsch
 * @copyright N/A
 * @link      https://puffin.dev
 * @package   PdfManager
 * @since     1.0.0
 */

namespace Craft;

class PdfManagerPlugin extends BasePlugin
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
         return Craft::t('PdfManager');
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return Craft::t('Populate editable fields in a PDF form with craft data.');
    }

    /**
     * @return string
     */
    public function getDocumentationUrl()
    {
        return 'NA';
    }

    /**
     * @return string
     */
    public function getReleaseFeedUrl()
    {
        return 'NA';
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
        return 'Kenneth Huebsch';
    }

    /**
     * @return string
     */
    public function getDeveloperUrl()
    {
        return 'https://puffin.dev';
    }

    /**
     * @return bool
     */
    public function hasCpSection()
    {
        return false;
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
}
