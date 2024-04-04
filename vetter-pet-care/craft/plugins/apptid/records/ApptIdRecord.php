<?php
/**
 * Created by PhpStorm.
 * User: angelachan
 * Date: 8/10/17
 * Time: 10:03 AM
 */

namespace Craft;


class ApptIdRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'apptrecord';
    }

    public function defineAttributes()
    {
        return array(
            'apptid' => array(AttributeType::Number,'column' => ColumnType::PK),
            'customerid' => array(AttributeType::Number),
            'requesttime' => array(AttributeType::Name),
        );
    }

    public function primaryKey()
    {
        return 'apptid';
    }

    public function createTable()
    {
        // Let the base class do the actual work
        parent::createTable();

        // Change the PK auto-increment starting point to 1001 pre client request
        craft()->db->createCommand()->setText('ALTER TABLE craft_apptrecord AUTO_INCREMENT=10001')->execute();

    }


}