<?php
/**
 * Created by JetBrains PhpStorm.
 * User: meabed
 * Date: 11/16/12
 * Time: 1:28 AM
 * To change this template use File | Settings | File Templates.
 */ 
class Web_Ppfix_Model_Config extends Mage_Paypal_Model_Config {
    protected $_supportedCurrencyCodes = array('AUD', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MXN',
        'NOK', 'NZD', 'PLN', 'GBP', 'SGD', 'SEK', 'CHF', 'USD', 'TWD', 'THB','AED');

}