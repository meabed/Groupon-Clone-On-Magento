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

/*
 * eWAY 3D-Secure Checkout Controller
 */
class ND_MigsVpc_MerchantController extends ND_MigsVpc_Controller_Abstract
{
    protected $_redirectBlockType = 'migsvpc/merchant_redirect';
    
    /*public function indexAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }

        try {
            $data = $this->getRequest()->getPost();
            Mage::getModel('migsvpc/ipn')->processIpnRequest($data, new Varien_Http_Adapter_Curl());
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }*/
    
    public function responseAction()
    {
        $responseParams = $this->getRequest()->getParams(); 
        //Mage::getModel('migsvpc/ipn')->processIpnRequest($responseParams, new Varien_Http_Adapter_Curl());  
        //echo '<pre>';print_r($responseParams);die;
        /*$this->loadLayout();  
        $this->renderLayout();*/ 
        if($responseArray['vpc_AcqResponseCode']=='00' && $responseArray['vpc_TxnResponseCode']=='0')
        {
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('core')->__($responseParams['vpc_Message']));
            //Mage::getModel('migsvpc/ipn')->processIpnRequest($responseParams, new Varien_Http_Adapter_Curl());
            $this->_redirect('checkout/onepage/success');
            return;
        }
        elseif($responseParams['vpc_TxnResponseCode']!='0')
        {
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__($responseParams['vpc_Message']));
            $this->_redirect('checkout/cart');
            return;
        }
        else
        {
            $this->_redirect('checkout/cart');
            return;
        }
    }
}
