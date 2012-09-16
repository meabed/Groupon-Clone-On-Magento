<?php
class Web_Voucher_Model_Mysql4_Vouchers extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        $this->_init('voucher/vouchers','entity_id');
    }
}