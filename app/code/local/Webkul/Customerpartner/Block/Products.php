<?php

class Webkul_Customerpartner_Block_Products extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        $this->_controller = 'products';
        $this->_headerText = Mage::helper('customerpartner')->__('Customers Products');
        $this->_blockGroup = 'customerpartner';

        parent::__construct();

        $this->_removeButton('add');

        $this->_addButton('suo', array(
                'label'     => 'Show Unapproved Only',
                'onclick'   => 'setLocation(\'' . $this->getShowUnapprovedOnlyUrl() .'\')',
                'class'     => '',
        ));
    }

	public function getShowUnapprovedOnlyUrl()
	{
		return $this->getUrl('*/*/index/ff/suo');
	}
}

