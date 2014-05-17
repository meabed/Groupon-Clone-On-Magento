<?php
/**
 * Customer Credit form block (Step2)
 *
 * @category   MW
 * @package    MW_Credit
 * @author Mage World <support@mage-world.com>
 */
class MW_Credit_Block_Adminhtml_Customer_Edit_Tab_Credit extends Mage_Adminhtml_Block_Widget_Form
{
	
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mw_credit/customer/tab/credit.phtml');
    }
    
    /**
     * Colection Order is refund
     * 
     * @return array
     */
	private function _getCreditmemo()
    {
    	$arr = array();
    	$collection = Mage::getResourceModel('sales/order_collection')
			->addAttributeToSelect('*')						
			->addAttributeToFilter('customer_id', Mage::registry('current_customer')->getId());
		foreach($collection as $order){ 
			if($order['status'] == Mage_Sales_Model_Order::STATE_CLOSED)
				$arr[$order['increment_id']] = $order['increment_id'];
		} 
		return $arr;
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_credit');
        $customer = Mage::registry('current_customer');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('credit')->__('Credit Information')));

        $fieldset->addField('credit', 'text', array(
           	'label' => Mage::helper('credit')->__('Credit'),
           	'name'  => 'credit',
       	));
       	
       	$fieldset->addField('amount_credit', 'text', array(
           	'label' => Mage::helper('credit')->__('Amount of Credits'),
           	'name'  => 'amount_credit',
       		'note' => Mage::helper('catalog')->__('Number of Credits which you want to add or subtract'),
       		'class' => 'validate-number',
       	));
        
       	$yesnoSource = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();
       	$fieldset->addField('is_refund_product', 'select', array(
            'name' => 'is_refund_product',
            'label' => Mage::helper('catalog')->__('Product Refunding'),
            'title' => Mage::helper('catalog')->__('Product Refunding'),
            'note' => Mage::helper('catalog')->__('Default is No. Choose Yes if is refund product'),
            'values' => $yesnoSource,
 
        ));
        
       	$fieldset->addField('creditmemo', 'select', array(
            'name' => 'creditmemo',
            'label' => Mage::helper('catalog')->__('Credit Memo'),
            'title' => Mage::helper('catalog')->__('Credit Memo'),
            'values' => $this->_getCreditmemo(),
        ));
        
        if ($customer->isReadonly()) {
            $form->getElement('credit')->setReadonly(true, true);
        }

        if(Mage::registry('current_customer'))
        {
        	$credit = Mage::getModel('credit/creditcustomer')->load($customer->getId())->getData('credit');
        	$form->getElement('credit')->setValue($credit);
        }
        
        // if not exist credit memo
        if (!$this->_getCreditmemo()){
        	 $form->getElement('is_refund_product')->setDisabled(1);
        	 $form->getElement('creditmemo')->setDisabled(1);
        }
        
        $form->getElement('credit')->setDisabled(1);
        $this->setForm($form);
        return $this;
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()->createBlock('credit/adminhtml_customer_edit_tab_credit_grid','credit.grid')
        );
        return parent::_prepareLayout();
    }

}
