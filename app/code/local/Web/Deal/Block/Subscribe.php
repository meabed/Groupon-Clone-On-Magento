<?php

class Web_Deal_Block_Subscribe extends Mage_Core_Block_Template
{
    protected $_cities;

    public function _construct()
    {
        $model = new Web_Deal_Model_System_Config_Source_Categories();
        $this->_cities = $model->toOptionArray();
    }
}
