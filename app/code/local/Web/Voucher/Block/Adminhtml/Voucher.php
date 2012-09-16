<?php
class Web_Voucher_Block_Adminhtml_Voucher extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_voucher';
        $this->_blockGroup = 'voucher';
        $this->_headerText = Mage::helper('voucher')->__('Voucher Manager');
//        $this->_addButtonLabel = Mage::helper('voucher')->__('Add Voucher');
        parent::__construct();
        $this->removeButton('add');
    }

}