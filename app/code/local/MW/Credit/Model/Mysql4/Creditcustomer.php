<?php

class MW_Credit_Model_Mysql4_Creditcustomer extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('credit/creditcustomer', 'customer_id');
    }
}