<?php namespace Zenbu\frameworks\craft;

use Craft;
use Zenbu\frameworks\craft\Base as Base;
use Zenbu\frameworks\craft\Cache;
use Zenbu\frameworks\craft\Convert;
use Zenbu\frameworks\craft\SectionBase;
use Zenbu\frameworks\craft\Session;
use Zenbu\librairies;

class FieldsBase extends Base
{
	var $std_fields;

	public function __construct()
	{
		$this->std_fields = array(
            'title' => array(
                'name'         => Lang::t('Title'),
                'handle'       => 'title',
                'query_handle' => 'title',
                'type'         => '-'
                ),
            'id'    => array(
                'name'         => Lang::t('ID'),
                'handle'       => 'id',
                'query_handle' => 'id',
                'type'         => '-'
                ),
            'uri' => array(
                'name'         => Lang::t('URI'),
                'handle'       => 'uri',
                'query_handle' => 'uri',
                'type'         => '-'
                ),
            'section' => array(
                'name'         => Lang::t('Section'),
                'handle'       => 'section',
                'query_handle' => 'section',
                'type'         => '-'
                ),
            'status' => array(
                'name'         => Lang::t('Status'),
                'handle'       => 'status',
                'query_handle' => 'status',
                'type'         => '-'
                ),
            'postDate' => array(
                'name'         => Lang::t('Post Date'),
                'handle'       => 'postDate',
                'query_handle' => 'postDate',
                'type'         => '-'
                ),
            'expiryDate' => array(
                'name'         => Lang::t('Expiry Date'),
                'handle'       => 'expiryDate',
                'query_handle' => 'expiryDate',
                'type'         => '-'
                ),
            'dateCreated' => array(
                'name'         => Lang::t('Create Date'),
                'handle'       => 'dateCreated',
                'query_handle' => 'dateCreated',
                'type'         => '-'
                ),
            'dateUpdated' => array(
                'name'         => Lang::t('Update Date'),
                'handle'       => 'dateUpdated',
                'query_handle' => 'dateUpdated',
                'type'         => '-'
                ),
            );

        if(Craft\craft()->edition != 0)
        {
            $this->std_fields['author'] = array(
                    'name'         => Lang::t('Author'),
                    'handle'       => 'author',
                    'query_handle' => 'authorId',
                    'type'         => '-'
                    );
        }
	} // END __construct()

    // --------------------------------------------------------------------


    /**
     * Retrieve field data
     * @return array Field data, organized by section/entryType
     */
	public function getFields()
	{
        if($out = Cache::get('getFields'))
        {
            return $out;
        }

		$sql = Craft\craft()->db->createCommand()
                    ->select('et.*')
                    ->from('entrytypes et')
                    ->queryAll();

        $output = array();
        $field_output = array();

        if(count($sql > 0))
        {
            foreach($sql as $row)
            {
                // $fieldLayout = Craft\craft()->fields->getLayoutById( $row['fieldLayoutId'] );
                // $fields      = $fieldLayout->getFields();
                $fields = Craft\craft()->fields->getLayoutFieldsById( $row['fieldLayoutId'] );

                foreach($fields as $f)
                {
                    $field_output[$f->field->id] = $f->field->getAttributes();
                }

                //    ----------------------------------------
                //    Add structure order "field" if section
                //    is a Structure
                //    ----------------------------------------
                $s = $this->getSections();
                foreach($s as $s)
                {
                    if($s->id == $row[Convert::col('sectionId')] && $s->type == 'structure')
                    {
                        $field_output['structure'] = array(
                                                            'name'         => Lang::t('Structure Order'),
                                                            'handle'       => 'structureOrder',
                                                            'query_handle' => 'structure',
                                                            'type'         => '-'
                                                            );
                    }
                }

                $output[Convert::col('sectionId').'_'.$row[Convert::col('sectionId')]][Convert::col('subSectionId').'_'.$row['id']] = $this->std_fields + $field_output;

                $field_output = array();
            }

        }

        Cache::set('getFields', $output, 300);

        return $output;
	} // END getFields()

    // --------------------------------------------------------------------


    /**
     * Get a list of sections based on field ID
     * @param  int $field_id The field ID
     * @return array           The array of sections found
     */
    public function getSectionsFromField($field_id)
    {
        $output = FALSE;

        // Get field layoutId using
        // the provided field_id
        $sql = Craft\craft()->db->createCommand()
                    ->select('flf.*')
                    ->from('fieldlayoutfields flf')
                    ->where(array('fieldId' => $field_id))
                    ->queryAll();

        if(count($sql > 0))
        {
            foreach($sql as $row)
            {
                // Get the sectionId (and section attributes)
                // using the found layoutId
                $sql2 = Craft\craft()->db->createCommand()
                        ->select('et.*')
                        ->from('entrytypes et')
                        ->where(array('fieldLayoutId' => $row['layoutId']))
                        ->queryAll();

                if(count($sql2 > 0))
                {
                    foreach($sql2 as $row2)
                    {
                        $output[$row2['sectionId']] = Craft\craft()->sections->getSectionById($row2['sectionId']);
                    }
                }
            }
        }

        return $output;
    } // END getSectionsFromField()

    // --------------------------------------------------------------------


    /**
     * Get all field data
     * @return array
     */
    public function getCustomFields()
    {
        $allFieldsPerSection = $this->getAllFields();

        $all_fields = $this->flattenFields($allFieldsPerSection);

        $output = FALSE;

        foreach($all_fields as $f)
        {
            $output[] = $f;
        }

        return $output;
    } // END getCustomFields()

    // --------------------------------------------------------------------


    /**
     * Remove the section/subsection part of the field array
     * @param  array $fieldsPerSection An array of fields classified per section/subsection
     * @return array                   An array of fields
     */
    public function flattenFields($fieldsPerSection)
    {
        $out = array();

        foreach($fieldsPerSection as $section => $arr1)
        {
            foreach($arr1 as $subsection => $fields)
            {
                foreach($fields as $field)
                {
                    $out[] = $field;
                }
            }
        }

        return $out;
    } // END flattenFields()

    // --------------------------------------------------------------------


    /**
     * Get Category data related to Category fields
     * @return array
     */
    public function getCategoryFieldData()
    {
        $fields = $this->getCustomFields();

        $categoryFields = FALSE;

        foreach($fields as $f)
        {
            if($f['type'] == 'Categories' && ! isset($categoryFields[$f['id']]))
            {
                $categoryFields[$f['id']] = $f;
                $categoryGroupId = substr($f['settings']['source'], 6); // eg. group:5
                $categoryFields[$f['id']]['categoryGroup'] = Craft\craft()->categories->getGroupById($categoryGroupId)->getAttributes();

                $criteria = Craft\craft()->elements->getCriteria(Craft\ElementType::Category);
                $criteria->groupId = $categoryGroupId;
                $criteria->order = 'lft';
                $criteria->limit = NULL;
                $categories = $criteria->find();

                $categoryFields[$f['id']]['categories'] = FALSE;

                foreach($categories as $category)
                {
                    $categoryFields[$f['id']]['categories'][$category->id] =  array(
                        'id'    => $category->id,
                        'title' => $category->title,
                        'slug'  => $category->slug,
                        );
                }
            }
        }

        return $categoryFields;
    } // END getCategoryFieldData

    // --------------------------------------------------------------------
}