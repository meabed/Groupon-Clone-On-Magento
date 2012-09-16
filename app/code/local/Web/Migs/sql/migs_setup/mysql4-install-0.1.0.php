<?php

$installer = $this;

$installer->startSetup();

$installer->run("
-- DROP TABLE IF EXISTS {$this->getTable('migs_info')};
CREATE TABLE ".$this->getTable('migs_info')." (
  `entity_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `info` text NOT NULL,
  `cc_type` varchar(10) NOT NULL,
  `cc_last_4` varchar(4) NOT NULL,
  `message` varchar(255) NOT NULL,
  `receipt_no` varchar(90) NOT NULL,
  PRIMARY KEY (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ");

$installer->endSetup();
