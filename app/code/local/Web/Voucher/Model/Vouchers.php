<?php
class Web_Voucher_Model_Vouchers extends Mage_Core_Model_Abstract
{
    const STATUS_USED = 'used';
    const STATUS_EXPIRED = 'expired';
    const STATUS_REDEEMED = 'redeemed';
    const STATUS_FRAUD = 'fraud';
    const STATUS_ACTIVE = 'active';
    const STATUS_CANCELED = 'canceled';

    public function _construct()
    {
        parent::_construct();
        $this->_init('voucher/vouchers');
    }

    public function sendVouchers()
    {
        Mage::log('Cron Running');
    }

    public function getStatuses()
    {
        return array(
            self::STATUS_ACTIVE => self::STATUS_ACTIVE,
            self::STATUS_CANCELED => self::STATUS_CANCELED,
            self::STATUS_USED => self::STATUS_USED,
            self::STATUS_EXPIRED => self::STATUS_EXPIRED,
            self::STATUS_REDEEMED => self::STATUS_REDEEMED,
            self::STATUS_FRAUD => self::STATUS_FRAUD
        );
    }
}