<?php namespace Zenbu\frameworks\craft;

use Craft;
use Craft\ElementType as ElementType;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;

class UserBase
{
	public static function getAuthors()
	{
		if($output = Cache::get('getAuthors'))
        {
            return $output;
        }

        $criteria = Craft\craft()->elements->getCriteria(ElementType::User);
        $criteria->limit = null;
        $output = $criteria->find();

        Cache::set('getAuthors', $output, 600);

        return $output;
	}
}