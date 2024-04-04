<?php namespace Zenbu\librairies;

use Craft;
use Zenbu\frameworks\craft\Base as Base;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\Db;
use Zenbu\frameworks\craft\Lang;
use Zenbu\frameworks\craft\Request;
use Zenbu\frameworks\craft\SectionBase as SectionBase;
use Zenbu\frameworks\craft\Session;

class Sections extends Base
{
    public function __construct()
    {
        $this->sectionBase = new SectionBase();
    }

    /**
     * Get sections
     * @return object Sections
     */
    public function getSections()
    {
        if(! isset($this->sections))
        {
            return $this->sectionBase->getSections();
        }

        return parent::getSections();
    } // END getSections()

    // --------------------------------------------------------------------


    /**
     * Get subsections of a section
     * @param  integer $section_id Main section ID
     * @return object              Subsections
     */
    public function getSubSections($section_id = 0)
    {
        return $this->sectionBase->getSubSections($section_id);
    } // END getSubSections()

    // --------------------------------------------------------------------


    /**
     * Retrieve a list of sections for settings select dropown
     * @return array The section array
     */
    public function getSectionSelectOptions()
    {
        $sections         = $this->getSections();
        $dropdown_options = $this->sectionBase->buildSelectOptions($sections);

        return $dropdown_options;

    } // END getSectionSelectOptions

    // --------------------------------------------------------------------
}