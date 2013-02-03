<?php
/**
 * ND MigsVpc payment gateway
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so you can be sent a copy immediately.
 *
 * Original code copyright (c) 2008 Irubin Consulting Inc. DBA Varien
 *
 * @category ND
 * @package    ND_MigsVpc
 * @copyright  Copyright (c) 2010 ND MigsVpc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class ND_MigsVpc_MerchantnewController extends ND_MigsVpc_Controller_Abstract
{
    protected $_redirectBlockType = 'migsvpc/merchantnew_redirect';
    
    public function responseAction()
    {
        $responseParams = $this->getRequest()->getParams();   
        //echo '<pre>';print_r($responseParams);die;        
        //Mage::getSingleton('core/session')->setVpcResponse($responseParams);      
        $postResponseData = array();
        foreach($responseParams as $key => $val)
            $postResponseData[] = $key.':'.$val;
        
        //Mage::log('MIGS Response: '.implode(",",$postResponseData), null, 'migs_payment.log');
        Mage::log("\n".'MIGS Response: '.implode(",",$postResponseData)."\n\n--------------------\n", null, 'migs_payment.log');
        
        if($responseParams['vpc_TxnResponseCode']=='7')
        {
            Mage::register('isSecureArea', true);
            $order = Mage::getModel('sales/order')->loadByIncrementId($responseParams['vpc_OrderInfo']);
            $order->delete();
            Mage::unregister('isSecureArea');
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__($responseParams['vpc_Message']));
            $this->_redirect('checkout/cart');
            return;
        }
        elseif($responseParams['vpc_TxnResponseCode']=='0')
        {            
            Mage::getModel('migsvpc/merchantnew')->afterSuccessOrder($responseParams);
            //Mage::getSingleton('core/session')->addSuccess(Mage::helper('core')->__($responseParams['vpc_Message']));
            $this->_redirect('checkout/onepage/success');
            return;
        }
        else
        {
            Mage::register('isSecureArea', true);
            $order = Mage::getModel('sales/order')->loadByIncrementId($responseParams['vpc_OrderInfo']);
            $order->delete();
            Mage::unregister('isSecureArea');
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__($responseParams['vpc_Message']));
            $this->_redirect('checkout/cart');
            return;
        }
    }
}
