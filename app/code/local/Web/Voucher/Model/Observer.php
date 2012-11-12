<?php
class Web_Voucher_Model_Observer
{
    protected $_order;
    protected $_voucherCode;
    protected $_voucherId;
    protected $_productId;
    protected $_productPrice = false;
    protected $_productName;

    public function generateVoucher($observer)
    {
        //$now = Mage::getModel('core/date')->timestamp(time());
        $now = date('Y-m-d H:i:s');

        $orderId = $observer->getEvent()->getOrder()->getId();
        $storeId = Mage::app()->getStore()->getId();

        $this->_order = Mage::getSingleton('sales/order')->load($orderId);
        $orderIncrementId = $this->_order->getIncrementId();
        $items = $this->_order->getAllItems();

        foreach ($items as $itemId => $item) {

            $productId = $item->getProductId();
            $this->_productId = $productId;

            $qty = $item->getQtyToInvoice();

            for ($i = 0; $i < $qty; $i++) {
                $s = strtoupper(md5(uniqid(rand(), true)));
                $start = rand(0, strlen($s) - 7);
                //$this->_voucherCode = strtoupper($this->_order->getProtectCode() . '-' . substr($s, $start, 6));
                $this->_voucherCode = strtoupper(substr($this->_order->getIncrementId(),-5) . '-' . substr($s, $start, 6));
                $m = Mage::getModel('voucher/vouchers')
                    ->setDealVoucherCode($this->_voucherCode)
                    ->setStoreId($storeId)
                    ->setOrderId($orderId)
                    ->setProductId($productId)
                    ->setCustomerId(Mage::getSingleton('customer/session')->getCustomer()->getId())
                    ->setOrderIncrementId($orderIncrementId)
                    ->setCreatedAt($now)
                    ->setUpdatedAt($now)
                    ->setStatus(Web_Voucher_Model_Vouchers::STATUS_ACTIVE)
                    ->setIsSent('0')
                    ->setOrderCreatedAt($this->_order->getCreatedAt())
                    ->save();
                $this->_voucherId = $m->getEntityId();
                //$this->_sendVoucherEmail();

            }

        }


    }

    public function emptyCart($observer)
    {
        $nProducts = (int)Mage::getStoreConfig('deal/config/multi_cart');

        if ($nProducts == 0) {
            return;
        }

        //Mage::log('Called');
        //$productId = $observer->getEvent()->getProduct()->getId();
        $cartHelper = Mage::helper('checkout/cart');
        $items = $cartHelper->getCart()->getItems();

        foreach ($items as $item) {
            //          $cartProductId = $item->getProductId();
            $itemId = $item->getId();
            $itemIds[] = $itemId;
        }
        $itemIds = array_unique($itemIds);
        if(empty($itemIds))
        {
            return;
        }
        //Mage::log($itemIds);
        //        Mage::log(end($itemIds));
        if (count($itemIds) > $nProducts) {
            for ($j = 0; $j < $nProducts ; $j++) {
                $cartHelper->getCart()->removeItem($itemIds[$j])->save();
            }
        }
    }
    public function senOrderVouchersEamil($observer)
    {
        $incrementId = Mage::getSingleton('checkout/session')->getLastRealOrderId();
        $order = Mage::getModel('sales/order')->loadByIncrementId($incrementId);
        if(!in_array($order->getStatus(),explode(',',Mage::getStoreConfig('voucher_options/configs/order_status'))) ){
            return;
        }
        $vouchers = Mage::getModel('voucher/vouchers')->getCollection()->addFieldToFilter('order_increment_id',$incrementId);
        foreach ($vouchers as $voucher)
        {
            $this->_sendVoucherEmail($voucher,$order);
        }

    }
    public function _sendVoucherEmail($voucher = false,$order=false)
    {
        //return ;
        if($this->_productId){
            $_product = Mage::getModel('catalog/product')->load($this->_productId);
        }else if ($voucher){
            $_product = Mage::getModel('catalog/product')->load($voucher->getProductId());
            if(!$order){
                $order = Mage::getModel('sales/order')->load($voucher->getOrderId());
            }
            $this->_order = $order;
            $this->_voucherId = $voucher->getId();
        }

        if (!$this->_productPrice) {
            $this->_productPrice = (int)$_product->getPrice();
            $this->_productName = $_product->getName();
        }
        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $customerName = $this->_order->getCustomerFirstname() . ' ' . $this->_order->getCustomerLastname();
        $customerEmail = $this->_order->getCustomerEmail();
        $totalPaid = (int)$this->_order->getTotalPaid() . ' ' . $currency_code;

        $templateId = Mage::getStoreConfig('voucher_options/configs/email_template');
        $senderName = Mage::getStoreConfig('trans_email/ident_support/name');
        $senderEmail = Mage::getStoreConfig('trans_email/ident_support/email');
        $sender = array('name' => $senderName,
            'email' => $senderEmail);
        $recepientEmail = $customerEmail;
        $recepientName = $customerName;

        $storeId = Mage::app()->getStore()->getId();

        $vars = array('customerName' => $customerName,
            'customerEmail' => $customerEmail,
            'voucherCode' => $this->_voucherCode,
            'productPrice' => $this->_productPrice,
            'productName' => $this->_productName);
        $translate = Mage::getSingleton('core/translate');
        $mail = Mage::getSingleton('core/email_template')
            ->sendTransactional($templateId, $sender, $recepientEmail, $recepientName, $vars, $storeId);
        if ($mail->getSentSuccess()) {
            $nSent = 0;
            if($voucher){
                $nSent = $voucher->getIsSent()+1;
            }else{
                $nSent = 1;
            }
            Mage::getModel('voucher/vouchers')->load($this->_voucherId)
                ->setIsSent($nSent)
                ->save();
        }
        $translate->setTranslateInline(true);
    }
}