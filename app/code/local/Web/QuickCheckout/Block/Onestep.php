<?php

class Vinagento_Oscheckout_Block_Onestep extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
	public function getQuickCheckout()     
     { 
        if (!$this->hasData('quickcheckout')) {
            $this->setData('quickcheckout', Mage::registry('quickcheckout'));
        }
        return $this->getData('quickcheckout');
        
    }
}