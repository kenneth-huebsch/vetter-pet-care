<?php namespace Zenbu\frameworks\craft;

use Craft;

class View
{
	public static function includeJs($file)
	{
		if(is_array($file))
		{
			foreach($file as $f)
			{
				$output = Craft\craft()->templates->includeJsResource($f);
			}

			return $output;
		}
		else
		{
			return Craft\craft()->templates->includeJsResource($file);
		}
	}

	public static function includeJsInline($code)
	{
		if(is_array($code))
		{
			foreach($code as $c)
			{
				$output = Craft\craft()->templates->includeJs($c);
			}

			return $output;
		}
		else
		{
			return Craft\craft()->templates->includeJs($code);
		}
	}

	public static function includeCss($file)
	{
		if(is_array($file))
		{
			foreach($file as $f)
			{
				$output = Craft\craft()->templates->includeCssResource($f);
			}

			return $output;
		}
		else
		{
			return Craft\craft()->templates->includeCssResource($file);
		}
	}

	public static function includeCssInline($code)
	{
		if(is_array($code))
		{
			foreach($code as $c)
			{
				$output = Craft\craft()->templates->includeCss($c);
			}

			return $output;
		}
		else
		{
			return Craft\craft()->templates->includeCss($code);
		}
	}

	public static function renderString($str = '', $vars = array())
	{
		return Craft\craft()->templates->renderString($str, $vars);
	}
}