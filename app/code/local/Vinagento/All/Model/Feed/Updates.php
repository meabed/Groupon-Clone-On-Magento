<?php

class Vinagento_All_Model_Feed_Updates extends Vinagento_All_Model_Feed_Abstract
{

    /**
     * Retrieve feed url
     *
     * @return string
     */
    public function getFeedUrl()
    {
        return Vinagento_All_Helper_Config::UPDATES_FEED_URL;
    }

    public function feedMew(Varien_Event_Observer $observer){
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            $this->touchMew();
        }
    }

    public function touchMew()
    {
        if (($this->getFrequency() + $this->getLastUpdate()) > time()) {
            return $this;
        }
        $feedData = array();
        try {

            $feedXml = $this->getFeedData();

            if ($feedXml && $feedXml->channel && $feedXml->channel->item) {
                foreach ($feedXml->channel->item as $item) {
                    $feedData[] = array(
                        'severity'      => 3,
                        'date_added'    => $this->getDate((string)$item->pubDate),
                        'title'         => (string)$item->title,
                        'description'   => (string)$item->description,
                        'url'           => (string)$item->link,
                    );
                }

                if ($feedData) {
                    Mage::getModel('adminnotification/inbox')->parse(array_reverse($feedData));
                }

            }
            $this->setLastUpdate();
            return true;
        } catch (Exception $E) {
            return false;
        }
    }

    /**
     * Retrieve Update Frequency
     *
     * @return int
     */
    public function getFrequency()
    {
        return (int)Vinagento_All_Helper_Config::FREQUENCY * 3600;
    }

    /**
     * Retrieve Last update time
     *
     * @return int
     */
    public function getLastUpdate()
    {
        return Mage::app()->loadCache('vgall_notifications_lastcheck');
    }

    /**
     * Set last update time (now)
     *
     * @return Mage_AdminNotification_Model_Feed
     */
    public function setLastUpdate()
    {
        Mage::app()->saveCache(time(), 'vgall_notifications_lastcheck');
        return $this;
    }
}