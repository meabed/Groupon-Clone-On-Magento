<?php
/**
 * admin customer left menu (Step1)
 *
 * @category   MW
 * @package    MW_Credit
 * @author Mage World <support@mage-world.com>
 */
class MW_Credit_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{
    protected function _beforeToHtml()
    {
    	if (Mage::helper('credit')->getEnabled()) {
        	$this->addTab('credit', array(
            	'label'     => Mage::helper('credit')->__('Credits'),
            	'content'   => $this->getLayout()->createBlock('credit/adminhtml_customer_edit_tab_credit')->initForm()->toHtml()
        	));
        }        
        return parent::_beforeToHtml();
    }
}
