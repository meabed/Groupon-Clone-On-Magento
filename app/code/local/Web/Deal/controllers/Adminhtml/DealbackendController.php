<?php

class Web_Deal_Adminhtml_DealbackendController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->__("Backend Page Title"));
        $this->renderLayout();
    }
}
