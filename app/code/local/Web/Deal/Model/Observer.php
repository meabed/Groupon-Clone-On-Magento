<?php
class Web_Deal_Model_Observer
{
    public function disableAddToCart( Varien_Event_Observer $observer )
    {
        $cart = $observer->getEvent()->getCart();
        $items = $cart->getItems();
        $productId = Mage::app()->getRequest()->getParam('product');
        $product = Mage::getModel('catalog/product')->load($productId);
        echo $product->getEndDate().'--';
        exit();
    }
}