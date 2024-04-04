<?php
/**
 * Twig Session plugin for Craft CMS
 *
 * Twig Session Variable
 *
 * @author    Matthew Cieslak
 * @copyright Copyright (c) 2017 Matthew Cieslak
 * @link      https://github.com/mattman93
 * @package   TwigSession
 * @since     1.0.0
 */

namespace Craft;

class TwigSessionVariable
{
    /**
     */
    public function add($key, $value)
    {
        craft()->httpSession->add($key, $value);
    }
    public function get($key)
    {
    return craft()->httpSession->get($key);
    }

    public function destroySession($key)
    {
      craft()->httpSession->remove($key);
      return; 
    }
}
