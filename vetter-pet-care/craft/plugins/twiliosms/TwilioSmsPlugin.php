<?php
/**
 * Twilio SMS Plugin plugin for Craft CMS
 *
 * Twilio SMS Plugin
 *
 * @author    Shoe Shine Design &amp; Development
 * @copyright Copyright (c) 2017 Shoe Shine Design &amp; Development
 * @link      http://shoeshinedesign.com/
 * @package   TwilioSmsPlugin
 * @since     1.0
 */

namespace Craft;

class TwilioSmsPlugin extends BasePlugin {
  /**
   * Called after the plugin class is instantiated; do any one-time initialization here such as hooks and events:
   *
   * craft()->on('entries.saveEntry', function(Event $event) {
   *    // ...
   * });
   *
   * or loading any third party Composer packages via:
   *
   * require_once __DIR__ . '/vendor/autoload.php';
   *
   * @return mixed
   */
  public function init() {
    $settings = craft()->plugins->getPlugin('twilioSms')->getSettings()->attributes;

    parent::init();

    // we only want to load the admin script in the admin section
    if(craft()->request->isCpRequest() && craft()->userSession->isLoggedIn()) {
      craft()->templates->includeJsResource('twiliosms/js/sms-admin.js');
    } else {
      // load the front-end script if AJAX is selected
      if($settings['ajaxOrRedirect'] === 'ajax') {
        craft()->templates->includeJsResource('twiliosms/js/sms-front.js');
      }
    }
  }

  /**
   * Returns the user-facing name.
   *
   * @return mixed
   */
  public function getName() {
    return Craft::t('Twilio SMS Plugin');
  }

  /**
   * Plugins can have descriptions of themselves displayed on the Plugins page by adding a getDescription() method
   * on the primary plugin class:
   *
   * @return mixed
   */
  public function getDescription() {
    return Craft::t('Twilio SMS Plugin');
  }

  /**
   * Returns the version number.
   *
   * @return string
   */
  public function getVersion() {
    return '1.0';
  }

  /**
   * Returns the developer’s name.
   *
   * @return string
   */
  public function getDeveloper() {
    return 'The Tactile Group';
  }

  /**
   * Returns the developer’s website URL.
   *
   * @return string
   */
  public function getDeveloperUrl() {
    return 'https://www.thetactilegroup.com/';
  }

  /**
   * Returns the settings as 'setting' => array(parameters)
   *
   * @return array
   */
  protected function defineSettings() {
    return array(
      'custApptReminderTxt' => array(AttributeType::String, 'required' => true),
      'custApptCancelledTxt' => array(AttributeType::String, 'required' => true),
      'vet24hrsApptNotificationTxt' => array(AttributeType::String, 'required' => true),
      'from'              => array(AttributeType::String, 'required' => true),
      'to'                => array(AttributeType::String, 'required' => false),
      'msgPrefix'         => array(AttributeType::String, 'required' => false),
      'msgPostfix'        => array(AttributeType::String, 'required' => false),
      'sid'               => array(AttributeType::String, 'required' => true),
      'authToken'         => array(AttributeType::String, 'required' => true),
      'ajaxOrRedirect'    => array(AttributeType::String, 'required' => false),
      'redirect'          => array(AttributeType::String, 'required' => false),
      'successMsg'        => array(AttributeType::String, 'required' => false),
      'numberMissingMsg'  => array(AttributeType::String, 'required' => false),
      'numberShortMsg'    => array(AttributeType::String, 'required' => false),
      'numberInvalidMsg'  => array(AttributeType::String, 'required' => false),
      'messageMissingMsg' => array(AttributeType::String, 'required' => false)
    );
  }

  /**
   * Returns the settings page HTML
   *
   * @return array
   */
  public function getSettingsHtml() {
    return craft()->templates->render('twiliosms/_settings', array(
      'settings' => $this->getSettings()
    ));
  }
}