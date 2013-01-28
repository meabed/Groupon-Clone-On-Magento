<?php

class Webkul_Customerpartner_Model_Entity_Setup extends Mage_Eav_Model_Entity_Setup
{
    public function getDefaultEntities()
    {
        return array(
        	'customerpartner_product' => array(
                'entity_model'      => 'customerpartner/product',
                'table'=>'customerpartner/entity',
                'attributes' => array(
               		'entity_id'         => array('type'=>'static'),
               		'name'    => array('type'=>'varchar'),
               		'description'    => array('type'=>'text'),
               		'short_description'    => array('type'=>'text'),
                    'fine_print'    => array('type'=>'text'),
                    'highlights'    => array('type'=>'text'),
                    'original_price'    => array('type'=>'decimal'),
                    'price'    => array('type'=>'decimal'),
                    'stock'        => array('type'=>'int'),
                    'weight'        => array('type'=>'decimal'),
                    'product_id'        => array('type'=>'int'),
                    'customer_id'        => array('type'=>'int'),
                    'start_date'        => array('type'=>'datetime'),
                    'end_date'        => array('type'=>'datetime'),
                    'voucher_date'        => array('type'=>'datetime'),
                    'created_at'        => array('type'=>'datetime')
              )
            )
        );
    }
}
