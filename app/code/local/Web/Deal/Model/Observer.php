<?php
class Web_Deal_Model_Observer
{
    public function disableAddToCart( Varien_Event_Observer $observer )
    {
        echo "X";exit();
    }
}