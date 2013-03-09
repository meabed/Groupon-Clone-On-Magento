<?php
class Web_Voucher_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function downloadAdmin($voucherCode =0,$auth =0)
    {

        //$voucherCode = $this->getRequest()->getParam('code');
        //$auth = strtoupper($this->getRequest()->getParam('auth'));
        $cS = '';
        $voucherId = Mage::getModel('voucher/vouchers')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('deal_voucher_code', $voucherCode)
            ->setPageSize(1)
            ->getFirstItem()
            ->getId();
        $voucher = Mage::getModel('voucher/vouchers')->load($voucherId);
        if ($voucher) {
            $cS = strtoupper(md5(strtoupper($voucher->getDealVoucherCode()) . '213@#$%^$DFSfwer@!#' . $voucher->getOrderId()));
        }
        if ($cS == $auth) {
            $order = Mage::getModel('sales/order')->load($voucher->getOrderId());
            $product = Mage::getModel('catalog/product')->load($voucher->getProductId());
            Mage::register('current_voucher', $voucher);
            Mage::register('current_order', $order);
            Mage::register('current_product', $product);
        }
        $this->loadLayout();
        $c = $this->getLayout()->getOutput();
        $mediaDir = Mage::getBaseDir('media') . DS . 'vouchers' . DS;
        $fname = $mediaDir . $auth . '.html';
        if (!file_exists($fname)) {
            $rs = file_put_contents($mediaDir . $auth . '.html', $c);
            if ($rs) {
                return $fname;
                exit();
            } else {
                return;
            }
        }
        echo $fname;
        return $fname;
        //exit();
    }
}