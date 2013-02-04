<?php
$installer = $this;
$installer->startSetup();
$installer->run("

DROP TABLE IF EXISTS {$this->getTable('mw_credit_history')};
CREATE TABLE {$this->getTable('mw_credit_history')} (
  `credit_history_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL default '0',
  `type_transaction` smallint(6) NOT NULL default '0',
  `transaction_detail` varchar(255) NOT NULL default '',
  `description` text NULL,
  `status` smallint(6) NULL default '0',
  `amount` int(11) NOT NULL default '0',
  `beginning_transaction` int(11) unsigned NOT NULL default '0',
  `end_transaction` int(11) unsigned NOT NULL default '0',
  `created_time` datetime NULL,
  PRIMARY KEY (`credit_history_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('mw_credit_customer')};
CREATE TABLE {$this->getTable('mw_credit_customer')} (
  `customer_id` int(11) unsigned NOT NULL default '0',
  `credit` int(11) unsigned NOT NULL default '0',
  `parent_id` int(11) unsigned NOT NULL default '0',
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS {$this->getTable('mw_credit_order')};
CREATE TABLE {$this->getTable('mw_credit_order')} (
  `order_id` int(11) unsigned NOT NULL default '0',
  `credit` int(11) NOT NULL default '0',
  `money` float(11) unsigned NOT NULL default '0',
  `credit_money_rate` varchar(11) NOT NULL,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$installer->endSetup(); 