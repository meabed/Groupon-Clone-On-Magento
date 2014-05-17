<?php

class MW_Credit_Model_Creditcustomer extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('credit/creditcustomer');
    }
    
   	public function saveCreditCustomer($customerData)
    {
    	$collection = Mage::getModel('credit/creditcustomer')->getCollection();
    	$write = Mage::getSingleton('core/resource')->getConnection('core_write');
    	$sql = 'INSERT INTO '.$collection->getTable('creditcustomer').' 
    				VALUES('.$customerData['customer_id'].','.$customerData['credit'].','.$customerData['parent_id'].')';
		$write->query($sql);
    }
}