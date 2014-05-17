<?php
class Web_States_Block_Adminhtml_States extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_states';
        $this->_blockGroup = 'web_states';
        $this->_headerText = Mage::helper('web_states')->__('Country/States Manager');
        parent::__construct();
    }
}