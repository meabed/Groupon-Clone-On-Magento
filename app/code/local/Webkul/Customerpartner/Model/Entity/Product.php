<?php

 class Webkul_Customerpartner_Model_Entity_Product extends Mage_Eav_Model_Entity_Abstract
{

    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('customerpartner_product');
        $read = $resource->getConnection('customerpartner_read');
        $write = $resource->getConnection('customerpartner_write');
        $this->setConnection($read, $write);
    }

}
