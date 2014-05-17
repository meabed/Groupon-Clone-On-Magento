<?php
class Web_States_Model_Resource_States extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('web_states/states', 'region_id');
    }
}