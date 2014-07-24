<?php

class Web_Voucher_Block_VoucherInfo extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->__('Voucher - %s', $this->getVoucher()->getDealVoucherCode()));
        }
    }

    public function getVoucher()
    {
        return Mage::registry('current_voucher');
    }

    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getProduct()
    {
        return Mage::registry('current_product');
    }


}
