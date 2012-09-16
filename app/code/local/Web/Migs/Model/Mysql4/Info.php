<?php
class Web_Migs_Model_Mysql4_Info extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
       $this->_init('migs/info', 'entity_id');
    }
    
}