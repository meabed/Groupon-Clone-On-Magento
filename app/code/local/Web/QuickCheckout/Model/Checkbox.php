<?php

class Web_QuickCheckout_Model_Checkbox extends Varien_Object
{
    const STATUS_ENABLED	= 1;
    const STATUS_DISABLED	= 0;
    const STATUS_ENABLED_CHECKED	= 2;

    static public function toOptionArray()
    {
        return array(
            self::STATUS_ENABLED            => Mage::helper('quickcheckout')->__('Enabled And Checked'),
            self::STATUS_ENABLED_CHECKED    => Mage::helper('quickcheckout')->__('Enabled But UnChecked'),
            self::STATUS_DISABLED           => Mage::helper('quickcheckout')->__('Disabled')
        );
    }
}