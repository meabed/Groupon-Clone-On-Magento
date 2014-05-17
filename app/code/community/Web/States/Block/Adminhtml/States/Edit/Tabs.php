<?php
class Web_States_Block_Adminhtml_States_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('web_states_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('web_states')->__('State Information'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('web_states')->__('State Information'),
            'title'     => Mage::helper('web_states')->__('State Information'),
            'content'   => $this->getLayout()->createBlock('web_states/adminhtml_states_edit_tab_form')->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}