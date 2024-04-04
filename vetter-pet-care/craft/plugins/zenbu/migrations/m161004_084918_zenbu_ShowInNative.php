<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m161004_084918_zenbu_ShowInNative extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		craft()->db->createCommand()->addColumnAfter('zenbu_display_settings', 'showInNative', array('maxLength' => 1, 'default' => false, 'required' => true, 'column' => 'tinyint', 'unsigned' => true), 'show');

		return true;
	}
}
