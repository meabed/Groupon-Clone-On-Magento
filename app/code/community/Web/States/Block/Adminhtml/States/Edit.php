<?php
class Web_States_Block_Adminhtml_States_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->_objectId = 'region_id';
        $this->_blockGroup = 'web_states';
        $this->_controller = 'adminhtml_states';
        $this->_updateButton('save', 'label', Mage::helper('web_states')->__('Save Item'));
        $this->_updateButton('delete', 'label', Mage::helper('web_states')->__('Delete Item'));

    }

    public function getHeaderText()
    {
        if (Mage::registry('state_data') && Mage::registry('state_data')->getRegionId()) {
            return Mage::helper('web_states')->__("Edit Item '%s'", $this->escapeHtml(Mage::registry('state_data')->getRegionId()));
        } else {
            return Mage::helper('web_states')->__('Add Item');
        }
    }
}
