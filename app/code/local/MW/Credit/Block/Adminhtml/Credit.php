<?php
class MW_Credit_Block_Adminhtml_Credit extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct() {
		$this->_controller = 'adminhtml_credit';
		$this->_blockGroup = 'credit';
		$this->_headerText = Mage::helper('credit')->__('Credits Manager');
			
		parent::__construct();
		$this->_removeButton('add');
	}
}