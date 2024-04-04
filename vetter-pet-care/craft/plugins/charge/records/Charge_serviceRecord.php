<?php
namespace Craft;

class Charge_serviceRecord extends BaseRecord
{
    public function getTableName()
    {
        return 'charge_serviceCharges';
    }

    protected function defineAttributes()
    {
        return array(
            
            'name' => AttributeType::String,
            'payment_fk_id' => Attribute::String,
            'description' => Attribute::String,
            'amount' => Attribute::String,
            'date' => Attribute::String,
            'customer_id' => Attribute::String,
            
        );
    }
}


?>