<?php
class Web_Deal_Model_Observer
{
    public function disableAddToCart( Varien_Event_Observer $observer )
    {
        $productId = Mage::app()->getRequest()->getParam('product');
        if($productId){
            if(Mage::helper('deal')->isProductExpired($productId))
            {
                Mage::throwException(
                    Mage::helper('deal')->__('Sorry, this deal has already expired')
                );
                return false;
            }
        }

    }
}