<?php
/**
 * Displaced Appointments plugin for Craft CMS
 *
 * Displaced Appointments Twig Extension
 *
 * @author    Matthew Cieslak
 * @copyright Copyright (c) 2017 Matthew Cieslak
 * @link      https://github.com/mattman93
 * @package   DisplacedAppointments
 * @since     1.0.0
 */

namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;

class DisplacedAppointmentsTwigExtension extends \Twig_Extension
{
    /**
     * @return string The extension name
     */
    public function getName()
    {
        return 'DisplacedAppointments';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'someFilter' => new \Twig_Filter_Method($this, 'someInternalFunction'),
        );
    }

    /**
    * @return array
     */
    public function getFunctions()
    {
        return array(
            'someFunction' => new \Twig_Function_Method($this, 'someInternalFunction'),
        );
    }

    /**
     * @return string
     */
    public function someInternalFunction($text = null)
    {
        $result = $text . " in the way";

        return $result;
    }
}