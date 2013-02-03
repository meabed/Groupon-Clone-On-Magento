<?php
class Web_Deal_Model_Observer
{
    public function disableAddToCart( Varien_Event_Observer $observer )
    {
        $cart = $observer->getEvent()->getCart();
        $items = $cart->getItems();
        foreach ($items as $item)
        {
            echo $item->getEndDate().'--';
        }
        exit();
    }
}