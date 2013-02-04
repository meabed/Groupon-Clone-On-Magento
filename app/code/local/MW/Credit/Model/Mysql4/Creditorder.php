<?php

class MW_Credit_Model_Mysql4_Creditorder extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('credit/creditorder', 'order_id');
    }
}