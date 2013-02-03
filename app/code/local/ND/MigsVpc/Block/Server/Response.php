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

class ND_MigsVpc_Block_Server_Response extends Mage_Core_Block_Template
{
    /**
     *  Return Error message
     *
     *  @return	  string
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('migsvpc/server/response.phtml');
    }
    
    public function getErrorMessage ()
    {
        $msg = Mage::getSingleton('checkout/session')->getMigsVpcErrorMessage();
        Mage::getSingleton('checkout/session')->unsMigsErrorMessage();
        return $msg;
    }

    /**
     * Get continue shopping url
     */
    public function getContinueShoppingUrl()
    {
        return Mage::getUrl('checkout/cart');
    }
}
