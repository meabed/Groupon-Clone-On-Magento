<?php

class Web_QuickCheckout_Helper_Data extends Mage_Core_Helper_Abstract
{
   
    
    public function getQuickCheckoutConfig($value) {
        
        return trim(Mage::getStoreConfig('quickcheckout/'.$value));
    }
    
     public function getQuickCheckoutLoading($for) {
        
      return  $this->getQuickCheckoutConfig('ajax_text/'.$for);
        
    }
    
    public function getQuickCheckoutskin() {
      if($color= $this->getQuickCheckoutConfig('css_style/heading')){
        return $color = "background:$color'";
      }
    }
    
    public function getQuickCheckoutHeadingCSS() {
        
      if($style= $this->getQuickCheckoutConfig('css_style/heading_text')){
        return $style;
      }
    }
    
    public function getQuickCheckoutbuttonCSS() {
      if($style= $this->getQuickCheckoutConfig('css_style/button')){
        return $style;
      }
    }
    
    public function getQuickCheckoutTitle() {
        return  $this->getQuickCheckoutConfig('general/title');
    }
    
    public function getQuickCheckoutContent() {
        if($this->getQuickCheckoutConfig('general/enable_content')){
             return  nl2br($this->getQuickCheckoutConfig('general/content'));
        }
       
    }
    
     public function getWidth() {
             if($data=(int)$this->getQuickCheckoutConfig('css_style/agreement')){
                return "width:" . $data . "px";
             }
           return;
    }
    
    public function getQuickCheckoutLogin() {
             if($data=$this->getQuickCheckoutConfig('general/login')){
                return $data;
             }
             else{
                return 'Already registered? Click to login.';
             }
       
    }
    public function getQuickCheckoutHeard() {
        
        if($data=$this->getQuickCheckoutConfig('quickheared/options_quickheared')){
           $Sources = explode(',', $data);
           return $Sources;
        }
        return;
    }
    
}
   