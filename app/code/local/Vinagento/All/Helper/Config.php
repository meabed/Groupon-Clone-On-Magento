<?php
/**
 * Vinagento Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.vinagento.com/LICENSE-M1.txt
 *
 * @category   Vinagento
 * @package    Vinagento_All
 * @copyright  Copyright (c) 2009-2010 Vinagento Co. (http://www.vinagento.com)
 * @license    http://www.vinagento.com/LICENSE-M1.txt
 */

class Vinagento_All_Helper_Config extends Mage_Core_Helper_Abstract
{
    /** Updates Feed path */
    const UPDATES_FEED_URL = 'http://www.vinagento.com/blog/category/news/feed';
    const FREQUENCY = 86400;
    /** EStore response cache key*/
    const STORE_RESPONSE_CACHE_KEY = 'vg_all_store_response_cache_key';
}
