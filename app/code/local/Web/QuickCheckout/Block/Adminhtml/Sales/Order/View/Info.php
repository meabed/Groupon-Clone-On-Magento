<?php

	//require_once BP.DS.'app'.DS.'code'.DS.'core'.DS.'Mage'.DS.'Adminhtml'.DS.'Block'.DS.'Sales'.DS.'Order'.DS.'View'.DS.'Info.php';
	class Web_QuickCheckout_Block_Adminhtml_Sales_Order_View_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info{
		
		protected $order;
		protected function _afterToHtml($html)
	    {
	    	
	    	if($this->getChild('quickcheckout_info')){
	    	
	    	$html .= $this->getChild('quickcheckout_info')->toHtml();
	    	
	    	}
	    	
	        return parent::_afterToHtml($html);
	    }
		//public function getOrder(){
		//	if(is_null($this->order)){
		//		
		//        if(Mage::registry('current_order')) {
		//        	
		//            $order = Mage::registry('current_order');
		//            
		//        }elseif(Mage::registry('order')) {
		//        	
		//            $order = Mage::registry('order');
		//            
		//        }else{
		//        	
		//        	$order = new Varien_Object();
		//        	
		//        }
		//        
		//        $this->order = $order;
		//	}
		//	
		//	return $this->order;
		//}
		
		
	}