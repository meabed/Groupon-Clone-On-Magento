<?php

class Web_QuickCheckout_Model_Required extends Varien_Object
{
    const STATUS_ENABLED_REQUIRED	= 1;
    const STATUS_DISABLED	= 0;
    const STATUS_ENABLED_OPTIONAL	= 2;

    static public function toOptionArray()
    {
        return array(
            self::STATUS_ENABLED_REQUIRED            => Mage::helper('quickcheckout')->__('Enabled And Required'),
            self::STATUS_ENABLED_OPTIONAL    => Mage::helper('quickcheckout')->__('Enabled But Optional'),
            self::STATUS_DISABLED           => Mage::helper('quickcheckout')->__('Disabled')
        );
    }
}