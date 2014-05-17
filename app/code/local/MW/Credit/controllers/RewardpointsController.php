<?php
class MW_Credit_RewardPointsController extends Mage_Core_Controller_Front_Action
{
    public function checkAction()
    {
		if(Mage::helper('credit')->getRewardPointsModule()){
			$this->getResponse()->setBody("1");	// exits module
		}else{
			$this->getResponse()->setBody("0");
		}
    }
}