<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\frameworks\craft\Base as Base;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\Session;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\Db;
use Zenbu\library;

class Filters extends Base
{
    var $fields;

    public function __construct()
    {
    }

    public function getFirstFilterOptions()
    {
        $sections = $this->getSections();

        // Using a non-numerical parameter will
        // output standard fields only, even if GET/POST present
        $default_fields = $this->getDefaultFields();
        unset($default_fields['section']); // Don't need this, we have it as the top dropdown

        foreach($default_fields as $handle => $field)
        {
            $output[0][0][Lang::t('Default')][$handle] = $field['name'];
        }

        foreach($sections as $section)
        {
            $subsections = $section->entryTypes;

            foreach($subsections as $subsection)
            {
                $filter_fields = $this->getFieldsObj()->getFieldsPerSection($section->id, $subsection->id);
                unset($filter_fields['section']); // Don't need this, we have it as the top dropdown

                foreach($filter_fields as $handle => $field)
                {
                    if(array_key_exists($handle, $default_fields))
                    {
                        $optgroup = Lang::t('Default');
                    }
                    else
                    {
                        $optgroup = Lang::t('Custom Fields');
                    }
                    $output[$section->id][$subsection->id][$optgroup][$handle] = $field['name'];
                }
            }
        }

        return $output;
    } // END getFirstFilterOptions()

    // --------------------------------------------------------------------


    /**
     * Retrieve Order By dropdown
     * @return array
     */
    public function getOrderBySelectOptions()
    {
       $sections = $this->getSections();

        // Using a non-numerical parameter will
        // output standard fields only, even if GET/POST present
        $default_fields = $this->getDefaultFields();
        unset($default_fields['section']); // Don't need this, we have it as the top dropdown

        foreach($default_fields as $handle => $field)
        {
            $output[0][0][Lang::t('Default')][$handle] = $field['name'];
        }

        foreach($sections as $section)
        {
            $subsections = $section->entryTypes;

            foreach($subsections as $subsection)
            {
                $filter_fields = $this->getFieldsObj()->getFieldsPerSection($section->id, $subsection->id);
                unset($filter_fields['section']); // Don't need this, we have it as the top dropdown

                foreach($filter_fields as $handle => $field)
                {
                    if(array_key_exists($handle, $default_fields))
                    {
                        $optgroup = Lang::t('Default');
                        $output[$section->id][$subsection->id][$optgroup][$handle] = $field['name'];
                    }

                    if(in_array($field['type'], array('PlainText', 'RichText', 'Date', 'Dropdown', 'RadioButtons', 'Color', 'Lightswitch', 'PositionSelect', 'Number')))
                    {
                        $optgroup = Lang::t('Custom Fields');
                        $output[$section->id][$subsection->id][$optgroup][$handle] = $field['name'];
                    }
                }
            }
        }

        return $output;

    } // END getOrderBySelectOptions

    // --------------------------------------------------------------------


    public static function getSecondFilterOptions()
    {
        $output['is'] = array(
            'is' => Lang::t('is'),
            );
        $output['is_isnot'] = array(
            'is'    => Lang::t('is'),
            'isnot' => Lang::t('is not'),
            );
        $output['date'] = array(
            'after'     => Lang::t('after'),
            'before'    => Lang::t('before'),
            'on'        => Lang::t('on'),
            'inthelast' => Lang::t('in the last'),
            'inthenext' => Lang::t('in the next'),
            'between'   => Lang::t('between'),
            );
        $output['contains'] = array(
            'contains'         => Lang::t('contains'),
            'beginswith'       => Lang::t('begins with'),
            'endswith'         => Lang::t('ends with'),
            'doesnotcontain'   => Lang::t('does not contain'),
            'doesnotbeginwith' => Lang::t('does not begin with'),
            'doesnotendwith'   => Lang::t('does not end with'),
            );
        $output['contains_plus_empty'] = array(
            'contains'         => Lang::t('contains'),
            'beginswith'       => Lang::t('begins with'),
            'endswith'         => Lang::t('ends with'),
            'doesnotcontain'   => Lang::t('does not contain'),
            'doesnotbeginwith' => Lang::t('does not begin with'),
            'doesnotendwith'   => Lang::t('does not end with'),
            'isempty'          => Lang::t('is empty'),
            'isnotempty'       => Lang::t('is not empty'),
            );
        $output['contains_doesnotcontain'] = array(
            'contains'         => Lang::t('contains'),
            'doesnotcontain'   => Lang::t('does not contain'),
            );
        $output['is_isnot_w_contains_doesnotcontain_labels'] = array(
            'is'         => Lang::t('contains'),
            'isnot'   => Lang::t('does not contain'),
            );

        return $output;
    } // END getSecondFilterOptions()

    // --------------------------------------------------------------------


    /**
     * Retrieve Show X results dropdown
     * @return array
     */
    public static function getLimitSelectOptions()
    {
        $output = array(
            //'1' => Lang::t('Show') . ' 1 ' . Lang::t('result'),
            //'2' => Lang::t('Show') . ' 2 ' . Lang::t('results'),
            '5'   => Lang::t('Show') . ' 5 ' . Lang::t('results'),
            '10'  => Lang::t('Show') . ' 10 ' . Lang::t('results'),
            '25'  => Lang::t('Show') . ' 25 ' . Lang::t('results'),
            '50'  => Lang::t('Show') . ' 50 ' . Lang::t('results'),
            '100' => Lang::t('Show') . ' 100 ' . Lang::t('results'),
            '200' => Lang::t('Show') . ' 200 ' . Lang::t('results'),
            '500' => Lang::t('Show') . ' 500 ' . Lang::t('results'),
            );

        return $output;

    } // END getLimitSelectOptions

    // --------------------------------------------------------------------




    /**
     * Retrueve Sort dropdown
     * @return  array
     */
    public function getSortSelectOptions()
    {
        return array('ASC' => Lang::t('Order by ascending'), 'DESC' => Lang::t('Order by descending'));
    } // END getSortSelectOptions

    // --------------------------------------------------------------------


    /**
     * Save the Saved Search in Cache for
     * future retrieval, eg. when returning to Zenbu
     * @return string JSON output
     */
    public function cacheFilters()
    {
        //  ----------------------------------------
        //  Set up variables
        //  ----------------------------------------

        $sectionId    = Request::param('sectionId', 0);
        $subSectionId = Request::param(Convert::col('subSectionId'), 0);
        $sectionId    = empty($sectionId) ? 0 : $sectionId;
        $subSectionId = empty($subSectionId) ? 0 : $subSectionId;

        $c = 0;
        $searchfilter                   = array();
        $searchfilter[$c]['filterAttribute1'] = 'sectionId';
        $searchfilter[$c]['filterAttribute2'] = 'is';
        $searchfilter[$c]['filterAttribute3'] = $sectionId;
        $c++;

        if($subSectionId != 0)
        {
            $searchfilter[$c]['filterAttribute1'] = Convert::col('subSectionId');
            $searchfilter[$c]['filterAttribute2'] = 'is';
            $searchfilter[$c]['filterAttribute3'] = $subSectionId;
            $c++;
        }

        //  ----------------------------------------
        //  Filters
        //  ----------------------------------------

        $filters = Request::param('filter');

        if( ! empty($filters) )
        {
            foreach($filters as $key => $val)
            {
                $searchfilter[$c]['filterAttribute1'] = $val['first'];
                $searchfilter[$c]['filterAttribute2'] = $val['second'];
                $searchfilter[$c]['filterAttribute3'] = isset($val['third']) ? $val['third'] : '';
                $c++;
            }
        }

        if(Request::param('limit'))
        {
            $searchfilter[$c]['filterAttribute1'] = 'limit';
            $searchfilter[$c]['filterAttribute2'] = Request::param('limit', '10');
            $searchfilter[$c]['filterAttribute3'] = '';
            $c++;
        }

        if(Request::param('orderby') && Request::param('sort'))
        {
            $searchfilter[$c]['filterAttribute1'] = 'orderby';
            $searchfilter[$c]['filterAttribute2'] = Request::param('orderby', 'title');
            $searchfilter[$c]['filterAttribute3'] = Request::param('sort', 'ASC');
            $c++;
        }

        if(Request::param('locale'))
        {
            $searchfilter[$c]['filterAttribute1'] = 'locale';
            $searchfilter[$c]['filterAttribute2'] = Request::param('locale', 'en');
            $searchfilter[$c]['filterAttribute3'] = '';
            $c++;
        }

        //    ----------------------------------------
        //    Cache it for 10 minutes, per User ID
        //    ----------------------------------------

        Cache::set('zenbu_filter_cache_' . Session::user()->id, $searchfilter, 600);

        $output['ok'] = TRUE;

        return $output;

    } // END saveFilters()

    // --------------------------------------------------------------------


    /**
     * Check if item is present in filter rows
     * @param  string   $str                The item name to search for in filter rows
     * @param  array    $filters            The search filter rows
     * @param  int      $filterAttribute    The filter dropdown to look into (first, second or third)
     * @return boolean          Item found or not
     */
    public static function isFilter($str, $filters, $filterAttribute = 1)
    {
        if(empty($str) || ! is_string($str) || ! in_array($filterAttribute, array(1,2,3)) || ! is_array($filters))
        {
            return FALSE;
        }

        foreach($filters as $key => $$filter)
        {
            if(isset($filter['filterAttribute' . $filterAttribute][$str]))
            {
                return TRUE;
            }
        }

        return FALSE;
    }

}