<?php
namespace Craft;

use Twig_Extension;
use Twig_Filter_Method;

class ZenbuTwigExtension extends \Twig_Extension
{
	public function getName()
    {
        return 'ZenbuTwigExtension';
    }

	public function getFilters()
	{
	    return array(
            'zenbuDump'                    => new Twig_Filter_Method($this, 'ZenbuDump'),
            'hookZenbuModifyFieldCellData' => new Twig_Filter_Method($this, 'hookZenbuModifyFieldCellData'),
            'craftVersionCompare'          => new Twig_Filter_Method($this, 'craftVersionCompare'),
            'renderCustomFieldDisplay'          => new Twig_Filter_Method($this, 'renderCustomFieldDisplay'),
            );
	}


    /**
     * A quick var dump (print_r) of passed data
     * @param mixed $data The item to var dump (eg. string, array, object)
     */
    public function ZenbuDump($data)
    {
        echo '<pre><code>';print_r($data);echo '</code></pre>';
    } // END ZenbuDump()

    // --------------------------------------------------------------------


    /**
     * hookZenbuModifyFieldCellData
     * Adds fieldtype display in Zenbu
     * from 3rd-party fields
     * @param  object   $field_object   The current field object, i.e. what you would get from {{my_field}} in a template
     * @param  array    $field          Array of data about the current field
     * @param  object   $entry          The current entry
     * @param  object   $entries        A list of current found entries
     * @return array                    An array containing the string to display in Zenbu for the current entry and field
     *                                  Key: 3rd-party plugin fieldtype name, Value: The string to display,
     *                                  eg. return array('MySuperField' => 'Hello')
     */
    public function hookZenbuModifyFieldCellData($field_object, $field, $entry, $entries)
    {
        //    ----------------------------------------
        //    Keep the Zenbu template path in memory.
        //    Some plugins might change and load a different
        //    path to load their own templates.
        //    ----------------------------------------
        $oldPath = craft()->path->getTemplatesPath();

        //    ----------------------------------------
        //    Get hook data
        //    ----------------------------------------
        $hook_array = craft()->plugins->call('ZenbuModifyFieldCellData', array('field_object' => $field_object, 'field' => $field, 'entry' => $entry, 'entries' => $entries));

        //    ----------------------------------------
        //    reload the original Zenbu template path
        //    ----------------------------------------
        craft()->path->setTemplatesPath($oldPath);

        //    ----------------------------------------
        //    Look for hook data for the current
        //    fieldtype only only
        //    ----------------------------------------
        foreach($hook_array as $class => $hook)
        {
            if(isset($hook[$field['type']]) && $hook[$field['type']] !== FALSE)
            {
                return $hook[$field['type']];
            }
        }

        return FALSE;

    } // END hookZenbuModifyFieldCellData()

    // --------------------------------------------------------------------

    /**
     * versionCompare
     * A quick version compare filter for craft version
     * @param  string  $current    Current Craft version
     * @param  string $target     The target version to compare
     * @param  string $comparator The comparator string, eg '<='
     * @return bool
     */
    public function craftVersionCompare($current = CRAFT_VERSION, $target = CRAFT_VERSION, $comparator = '=')
    {
        return version_compare($current, $target, $comparator);
    } // END craftVersionCompare()

    // --------------------------------------------------------------------

    public function renderCustomFieldDisplay($object, $str = '')
    {
        $vars['entry'] = $object;

        // Error catching that might come in handy when troubleshooting
        // the user-generated Twig code. May help get more details about
        // errors (eg. get the next exception message instead of
        // PHP's first "Array to string conversion" error (as an example))
        // Keeping this commented out for now.

        // Taking over error handling (eg. PHP errors)
        // set_error_handler(function($errno, $errstr, $errfile, $errline, $errcontext) {
        //     var_dump($errno, $errstr, $errfile, $errline, $errcontext);
        // });

        // Taking over exception handling (eg. thrown error exceptions)
        // set_exception_handler(function ($exception) {
        //     echo $exception->getMessage();
        // });

        return craft()->templates->renderString($str, $vars);
    }
}