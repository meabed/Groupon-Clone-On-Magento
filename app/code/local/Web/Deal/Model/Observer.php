<?php
class Web_Deal_Model_Observer
{
    public function disableAddToCart( Varien_Event_Observer $observer )
    {
        $productId = Mage::app()->getRequest()->getParam('product');
        if(Mage::helper('deal')->isProductExpired($productId))
        {
            Mage::throwException(
                Mage::helper('deal')->__('Sorry the Deal is already expired')
            );
            return false;
        }
    }
}