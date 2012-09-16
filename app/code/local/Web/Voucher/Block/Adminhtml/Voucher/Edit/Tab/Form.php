<?php
class Web_Voucher_Block_Adminhtml_Voucher_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('voucher_form', array('legend' => Mage::helper('voucher')->__('Voucher information')));

        $fieldset->addField('deal_voucher_code', 'text', array(
                                                              'label' => Mage::helper('voucher')->__('Code'),
                                                              'class' => 'required-entry',
                                                              'required' => true,
                                                              'name' => 'deal_voucher_code',
                                                              'readonly' => true,
                                                              'disabled' => false
                                                         ));

        $fieldset->addField('status', 'select', array(
                                                     'label' => Mage::helper('voucher')->__('Status'),
                                                     'name' => 'status',
                                                     'values' => Mage::getSingleton('voucher/vouchers')->getStatuses()
                                                ));

        if (Mage::getSingleton('adminhtml/session')->getVoucherData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getVoucherData());
            Mage::getSingleton('adminhtml/session')->setVoucherData(null);
        } elseif (Mage::registry('voucher_data')) {
            $form->setValues(Mage::registry('voucher_data')->getData());
        }
        return parent::_prepareForm();
    }
}
