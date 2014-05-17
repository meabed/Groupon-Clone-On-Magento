<?php

class MW_Credit_Model_Quote_Address_Total_Discount extends Mage_Sales_Model_Quote_Address_Total_Discount
{
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
    	$quote = $address->getQuote();
        $credit = Mage::helper('credit')->exchangeCreditToMoney(Mage::getSingleton('checkout/session')->getCredit());
		
        $subtotalWithDiscount= 0;
    	$items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }

        $address->setCreditDiscount($credit);
        $address->setSubtotalWithDiscount($subtotalWithDiscount - $credit);
        $address->setBaseCreditDiscount($credit);
        $address->setBaseSubtotalWithDiscount($subtotalWithDiscount - $credit);
		
        
        $address->setGrandTotal($address->getGrandTotal() - $address->getCreditDiscount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal()-$address->getBaseCreditDiscount());
        $address->setBaseDiscountAmount($address->getBaseDiscountAmount()-$address->getBaseCreditDiscount());
        return $this;
    }
	public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        //$amount = $address->getCreditDiscount();
        $amount = $address->getCreditDiscount();
        if ($amount!=0) {
            $title = Mage::helper('sales')->__('Credit Discount');
            $address->addTotal(array(
                'code'=>$this->getCode(),
                'title'=>$title,
                'value'=>-$amount
            ));
        }
        return $this;
    }
  
}