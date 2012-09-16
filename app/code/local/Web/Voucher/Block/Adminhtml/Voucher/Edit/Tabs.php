<?php
class Web_Voucher_Block_Adminhtml_Voucher_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('voucher_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('voucher')->__('Voucher Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('voucher')->__('Voucher Information'),
            'title'     => Mage::helper('voucher')->__('Voucher Information'),
            'content'   => $this->getLayout()->createBlock('voucher/adminhtml_voucher_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}