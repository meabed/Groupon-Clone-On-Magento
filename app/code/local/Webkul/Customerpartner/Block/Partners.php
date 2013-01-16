<?php

class Webkul_Customerpartner_Block_Partners extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'partners';
        $this->_headerText = Mage::helper('customerpartner')->__('Customers Names');
        $this->_blockGroup = 'customerpartner';

        parent::__construct();

        $this->_removeButton('add');
		$this->_removeButton('reset_filter_button');
		$this->_removeButton('search_button'); 


       
    }

	
}

