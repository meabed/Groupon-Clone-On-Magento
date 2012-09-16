<?php
class Web_Voucher_Model_Mysql4_Vouchers_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('voucher/vouchers');
    }
}