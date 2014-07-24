<?php

class Web_Voucher_Block_Adminhtml_Voucher_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'voucher';
        $this->_controller = 'adminhtml_voucher';
        $this->_updateButton('save', 'label', Mage::helper('voucher')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('voucher')->__('Delete Item'));
    }

    public function getHeaderText()
    {
        if (Mage::registry('voucher_data') && Mage::registry('voucher_data')->getId()) {
            return Mage::helper('voucher')->__("Edit Item '%s'", $this->escapeHtml(Mage::registry('voucher_data')->getDealVoucherCode()));
        } else {
            return Mage::helper('voucher')->__('Add Item');
        }
    }
}
