<?php
/**
 * Created by PhpStorm.
 * User: angelachan
 * Date: 8/10/17
 * Time: 11:10 AM
 */

namespace Craft;


class ApptIdModel
{
    public function __toString()
    {
        return (string)$this->id;
    }

    public function defineAtttributes()
    {
        return array(
            'id' => AttributeType::Number,
            'customerid' => AttributeType::Number,
            'requesttime' => AttributeType::DateTime,
            'dateCreated' => AttributeType::DateTime,
            'dateUpdated' => AttributeType::DateTime,
        );
    }




}