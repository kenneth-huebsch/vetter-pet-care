<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m161220_052711_zenbu_customDisplayStringTemplate extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		craft()->db->createCommand()->addColumnAfter('zenbu_display_settings', 'customDisplayStringTemplate', array('required' => false, 'column' => 'text', 'unsigned' => true), 'settings');

		return true;
	}
}
