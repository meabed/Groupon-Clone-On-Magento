<?php

class Web_QuickCheckout_Controller_Event
{
	//Event: adminhtml_controller_action_predispatch_start
	public function customTheme()
	{
		Mage::getDesign()->setArea('adminhtml')
			->setTheme((string)Mage::getStoreConfig('quickcheckout/css_style/admin'));
	}
}
