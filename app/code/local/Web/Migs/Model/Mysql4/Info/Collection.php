<?php
class Web_Migs_Model_Mysql4_Info_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('migs/info');
    }
    
}