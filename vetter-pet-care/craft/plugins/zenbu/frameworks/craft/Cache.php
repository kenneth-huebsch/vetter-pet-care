<?php namespace Zenbu\frameworks\craft;

use Craft;

class Cache
{
	public static function set($key, $value, $duration = 120)
	{
        Craft\craft()->cache->set('zenbu_'.$key, $value, $duration);
	}

	public static function get($key)
	{
		if(Craft\craft()->config->get('zenbuDisableCache'))
		{
			return FALSE;
		}

        return Craft\craft()->cache->get('zenbu_'.$key);
	}

	public static function delete($key)
	{
        return Craft\craft()->cache->delete('zenbu_'.$key);
	}
}