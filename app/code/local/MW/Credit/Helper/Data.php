<?php

class MW_Credit_Helper_Data extends Mage_Core_Helper_Abstract
{	
	
	/**
	 * get config
	 */
	public function getEnabled()
	{
		return Mage::getStoreConfig('credit/config/enabled');
	}
	public function getCreditMoneyRateConfig()
	{
		return Mage::getStoreConfig('credit/config/credit_money_rate');
	}
	public function getMaxCreditToCheckOut()
	{
		if(Mage::getStoreConfig('credit/options/max_credit_to_checkout'))
			return Mage::getStoreConfig('credit/options/max_credit_to_checkout');
		return 0;
	}
	public function allowSendCreditToFriend()
	{
		return Mage::getStoreConfig('credit/options_send_credit/allow_send_credit_to_friend');
	}
	public function getMaxRecipients()
    {
        if(Mage::getStoreConfig('credit/options_send_credit/max_recipients'))
			return Mage::getStoreConfig('credit/options_send_credit/max_recipients');
		return 3;
    }
	public function getMaxCreditToSend()
	{
		if(Mage::getStoreConfig('credit/options_send_credit/max_credit_to_send'))
			return Mage::getStoreConfig('credit/options_send_credit/max_credit_to_send');
		return 0;
	}
	
	/*
	 * go back URL
	 */ 
	public function getBackUrl()
    {
        $back_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $this->getUrl('customer/account/');
        return $back_url;
    }
	
	/**
	 * 
	 * @param $credit int
	 * @return float
	 */
	public function exchangeCreditToMoney($credit)
	{
		$rate = Mage::getStoreConfig('credit/config/credit_money_rate');
		$rate = explode('/',$rate);
	   	$money = ($credit * $rate[1])/$rate[0];
	   	return $money;
	}
	public function formatMoney($money)
	{
		return Mage::helper('core')->currency($money);
	}
    public function getCreditByCheckout()
    {
		$guest_credit=Mage::getSingleton('customer/session')->getGuestcredit();
    	return !empty($guest_credit)?$guest_credit:Mage::getSingleton('checkout/session')->getCredit();
    }
	 public function getGuestCredit()
    {		
    	return Mage::getSingleton('customer/session')->getGuestcredit();
    }
	public function getCreditByOrder($order)
	{
		return Mage::getModel('credit/creditorder')->load($order->getId())->getCredit();
	}
	public function getCreditByCustomer()
	{
		$customer = Mage::getSingleton("customer/session")->getCustomer();
		return Mage::getModel('credit/creditcustomer')->load($customer->getId())->getCredit();
	}
    public function getCreditRate()
	{
		$config = Mage::getStoreConfig('credit/config/credit_money_rate');
		$rate = explode("/",$config);
		return $rate;
	}
	/*
	public function getCreditPerPoint()
	{
		$config = Mage::getStoreConfig('credit/options_exchange_to_point/credit_point_rate');
		$rate = explode("/",$config);
		return $rate;
	}
	public function allowExchangeCreditToPoint()
	{
		return Mage::getStoreConfig('credit/options_exchange_to_point/enabled')
			   && $this->getRewardPointsModule();
	}
	public function getRewardPointsModule()
	{
		$modules = array_keys((array)Mage::getConfig()->getNode('modules')->children());
		if(in_array('MW_RewardPoints',$modules)) return true;	// exits module
		return false;
	}
	*/
    
    /**
     * Capcha Config
     */
	public function enabledCapcha()
	{
		return Mage::getStoreConfig('credit/capcha/capcha_enabled');
	}
	public function getCapchaImageWidth()
	{
		if(Mage::getStoreConfig('credit/capcha/image_width'))
			return Mage::getStoreConfig('credit/capcha/image_width');
		return 255;
	}
	public function getCapchaImageHeight()
	{
		if(Mage::getStoreConfig('credit/capcha/image_height'))
			return Mage::getStoreConfig('credit/capcha/image_height');
		return 50;
	}
	public function getCapchaBackgroundImage()
	{
		if(Mage::getStoreConfig('credit/capcha/background_image'))
			return Mage::getBaseDir('media').DS.'mw_credit'.DS.'capcha'.DS. Mage::getStoreConfig('credit/capcha/background_image');
		//return Mage::getDesign()->getSkinBaseDir(array()).DS.'mw_credit'.DS.'backgrounds'.DS.'bg3.jpg';
		return 0;
	}
	public function getCapchaPerturbation()
	{
		if(Mage::getStoreConfig('credit/capcha/perturbation'))
			return Mage::getStoreConfig('credit/capcha/perturbation');
		return 0.7;
	}
	public function getCapchaCodeLength()
	{
		if(Mage::getStoreConfig('credit/capcha/code_length'))
			return Mage::getStoreConfig('credit/capcha/code_length');
		return 7;
	}
	public function capchaUseTransparentText()
	{
		if(Mage::getStoreConfig('credit/capcha/use_transparent_text'))
			return Mage::getStoreConfig('credit/capcha/use_transparent_text');
		return 1;
	}
	public function getCapchaTextTransparencyPercentage()
	{
		if(Mage::getStoreConfig('credit/capcha/text_transparency_percentage'))
			return Mage::getStoreConfig('credit/capcha/text_transparency_percentage');
		return 0;
	}
	public function getCapchaNumberLine()
	{
		return Mage::getStoreConfig('credit/capcha/num_lines');
	}
}