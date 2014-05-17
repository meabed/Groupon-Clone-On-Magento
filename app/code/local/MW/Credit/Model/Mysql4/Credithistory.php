<?php

class MW_Credit_Model_Mysql4_Credithistory extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        $this->_init('credit/credithistory', 'credit_history_id');
    }
}