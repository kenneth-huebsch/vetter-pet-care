<?php
namespace Craft;

use League\OAuth2\Client\Grant\Password;

class PasswordEnforcerPlugin extends BasePlugin
{

	public function getName()
	{
		return Craft::t('Password Enforcer');
	}

	public function getVersion()
	{
		return '0.1';
	}

	function getDeveloper()
	{
		return 'STAPLEGUN';
	}

	public function getDeveloperUrl()
	{
		return 'http://staplegun.us';
	}

	public function getSettingsHtml()
	{
		return craft()->templates->render('passwordenforcer/_settings', array(
			'settings' => $this->getSettings()
		));
	}

	protected function defineSettings()
	{
		return array(
			'enforceLength' => array(AttributeType::Bool, 'label' => 'Enforce Minimum Length Requirement', 'default' => true),
			'minimumPasswordLength' => array(AttributeType::Number, 'label' => 'Minimum Password Length', 'default' => 10, 'required' => true),
			'enforceRegex' => array(AttributeType::Bool, 'label' => 'Enforce Regex Pattern Validation', 'default' => true),
			'passwordValidationRegex' => array(AttributeType::String, 'label' => 'Regular Expression Validation Pattern (Optional)', 'default' => '/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/', 'required' => true),
			'passwordValidationRegexErrorText' => array(AttributeType::String, 'label' => 'Regular Expression Validation Error Text', 'default' => 'Your password must contain letters, numbers and symbols.', 'required' => true)
		);
	}

	public function init()
	{
		craft()->on('users.beforeSetPassword', array($this, 'onBeforeSetPassword'));
	}

	public function onBeforeSetPassword(Event $event)
	{
		$settings = craft()->plugins->getPlugin('passwordEnforcer')->getSettings();
		$user = $event->params['user'];
		$password = $event->params['password'];

		$valid = true;

		if ($settings->enforceLength && mb_strlen($password) < $settings->minimumPasswordLength)
		{
			$valid = false;
			$erro_message = 'onBeforeSetPassword:  Your password must be at least ' . $settings->minimumPasswordLength . ' characters long.';
			$user->addError('newPassword', 'Your password must be at least ' . $settings->minimumPasswordLength . ' characters long.');
            PasswordEnforcerPlugin::log($erro_message,LogLevel::Info,true);
		}

        $hasUpper = preg_match('/[A-Z]/', $password);
		/* achan 09-08-2017 remove has special characters per customer request */
        //$hasSpecial = preg_match('/[^a-zA-Z\d]/', $password);

        if (!$hasUpper){
            $valid = false;
            $message = "Your password must contain 1 uppercase letter";
            $user->addError('newPassword', $message);
            PasswordEnforcerPlugin::log($message,LogLevel::Info,true);
            }

        /* Turn off in plugin page. to use this validation because it is not working */
//		if ($settings->enforceRegex && !preg_match($settings->passwordValidationRegex, $password))
//		{
//			$valid = false;
//			$message = $settings->passwordValidationRegexErrorText ? $settings->passwordValidationRegexErrorText : 'Your password does not meet the requirements. Contact your admin for clarification.';
//			$user->addError('newPassword', $message);
//		}

		if (!$valid)
		{
			$event->performAction = false;
		}
	}
}
