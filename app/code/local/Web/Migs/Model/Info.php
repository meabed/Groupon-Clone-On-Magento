<?php
class Web_Migs_Model_Info extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        //parent::_construct();
        //$this->_init('migs/info');
    }
    public static function checkMigsOrders()
    {
        Mage::log('Cron Ran',0,'cron.txt');
        if(!Mage::getSingleton('migs/paymentMethod')->getConfigData('api_active')){
            return ;
        }
        $period = (1 * 1 * 10 * 60);
        $from = date("Y-m-d H:i:s", strtotime(Mage::getModel('core/date')->gmtDate()) - $period);
        $to = date("Y-m-d H:i:s", strtotime(Mage::getModel('core/date')->gmtDate()));

        $orders = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('created_at',
            array('from' => $from, 'to' => $to))->addAttributeToFilter('status', array('neq' =>
                'canceled'));
        $orders->getSelect()->joinRight(array('payment' => 'sales_flat_order_payment'),
            'main_table.entity_id = payment.parent_id AND payment.method ="migs"', array('method'));
            
        foreach ($orders as $order)
        {
            //echo $order->getIncrementId();
            $return = Mage::helper('migs')->checkOrderPayment($order->getIncrementId());
            //print_r($return);exit();
            $o = Mage::getModel('sales/order')->load($order->getId());
            if($return['result'] === true)
            {
                $o->setState($o->getState(),$o->getStatus() , $return['message'])->save();
            }
            
            
            //Mage::log($order->getIncrementId() . ' - Checked', null, 'iid.txt');
        }
        return true;
    }
}