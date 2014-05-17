<?php
class Web_Voucher_Block_Recent extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        $ordersTable = Mage::getSingleton('core/resource')->getTableName('sales/order');
        $productTable = Mage::getResourceModel('catalog/product_flat')->getFlatTableName(1);
        $vouchers = Mage::getModel('voucher/vouchers')
                ->getCollection();
        $vouchers->getSelect()
                ->joinLeft(array('product' => $productTable), 'main_table.product_id = product.entity_id', array('name'));
        $vouchers->getSelect()
                ->joinLeft(array('orders' => $ordersTable), 'main_table.order_id=orders.entity_id', array('orders.customer_firstname', 'orders.customer_lastname', 'order_status'=>'orders.status'))
                ->where('orders.customer_id = ' . Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->where('main_table.customer_id = ' . Mage::getSingleton('customer/session')->getCustomer()->getId())
                ->order(array('main_table.created_at desc','main_table.order_id desc'))
                ->limit('10');
        $this->setVouchers($vouchers);
    }

    public function getViewUrl($voucher)
    {
        return $this->getUrl('/', array('voucher/view/code' => $voucher->getDealVoucherCode()));
    }

    public function getPrintUrl($voucher)
    {
        return $this->getUrl('voucher/view/print', array('code' => $voucher->getDealVoucherCode()));
    }

    public function getDownloadUrl($voucher)
    {
        return $this->getUrl('voucher/view/download', array('code' => $voucher->getDealVoucherCode()));
    }

    protected function _toHtml()
    {
        if ($this->getVouchers()->getSize() > 0) {
            return parent::_toHtml();
        }
        return '';
    }

}