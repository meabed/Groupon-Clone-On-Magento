<?php

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_aux')}` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `real_order_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `percent` decimal(10,2) NOT NULL,
  `amount_earned` decimal(10,2) NOT NULL,
  `amount_owed` decimal(10,2) NOT NULL,
  `cleared_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `real_order_id` (`real_order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_entity')}` (
  `entity_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_id` smallint(8) unsigned NOT NULL DEFAULT '0',
  `attribute_set_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `website_id` smallint(5) unsigned DEFAULT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `group_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `increment_id` varchar(50) NOT NULL DEFAULT '',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `store_id` smallint(5) unsigned DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`entity_id`),
  KEY `FK_CUSTOMERPARTNER_ENTITY_STORE` (`store_id`),
  KEY `IDX_ENTITY_TYPE` (`entity_type_id`),
  KEY `IDX_AUTH` (`email`,`website_id`),
  KEY `FK_CUSTOMERPARTNER_WEBSITE` (`website_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Customer Entityies' AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_entity_additionalinfo')}` (
  `userid` int(11) NOT NULL,
  `address` text NOT NULL,
  `postalcode` text NOT NULL,
  `city` text NOT NULL,
  `area` text NOT NULL,
  `addurl` text NOT NULL,
  `price` int(11) NOT NULL,
  `additional` text NOT NULL,
  `mobile` text NOT NULL,
  `addinfoid` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`addinfoid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_entity_data')}` (
  `index` int(11) NOT NULL AUTO_INCREMENT,
  `producttype` varchar(255) NOT NULL,
  `producttypecustom` varchar(255) NOT NULL,
  `mageproductid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `wstoreids` int(11) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `customerpartnerproductid` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  PRIMARY KEY (`index`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_entity_datapayment')}` (
 `autoid` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `paymentsource` varchar(255) NOT NULL,
  PRIMARY KEY (`autoid`),
  UNIQUE KEY `userid` (`userid`)
)  ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_entity_datetime')}` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type_id` smallint(8) unsigned NOT NULL DEFAULT '0',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `value` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`value_id`),
  KEY `FK_CUSTOMERPARTNER_DATETIME_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMERPARTNER_DATETIME_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMERPARTNER_DATETIME_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;




CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_entity_decimal')}` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type_id` smallint(8) unsigned NOT NULL DEFAULT '0',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `value` decimal(12,4) NOT NULL DEFAULT '0.0000',
  PRIMARY KEY (`value_id`),
  KEY `FK_CUSTOMERPARTNER_DECIMAL_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMERPARTNER_DECIMAL_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMERPARTNER_DECIMAL_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_entity_int')}` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type_id` smallint(8) unsigned NOT NULL DEFAULT '0',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `value` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`value_id`),
  KEY `FK_CUSTOMERPARTNER_INT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMERPARTNER_INT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMERPARTNER_INT_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_entity_text')}` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type_id` smallint(8) unsigned NOT NULL DEFAULT '0',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  PRIMARY KEY (`value_id`),
  KEY `FK_CUSTOMERPARTNER_TEXT_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMERPARTNER_TEXT_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMERPARTNER_TEXT_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_entity_userdata')}` (
  `autoid` smallint(6) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `wantpartner` smallint(6) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mageuserid` int(11) NOT NULL,
  PRIMARY KEY (`autoid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{$this->getTable('customerpartner_entity_varchar')}` (
  `value_id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_type_id` smallint(8) unsigned NOT NULL DEFAULT '0',
  `attribute_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `entity_id` int(10) unsigned NOT NULL DEFAULT '0',
  `value` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`value_id`),
  KEY `FK_CUSTOMERPARTNER_VARCHAR_ENTITY_TYPE` (`entity_type_id`),
  KEY `FK_CUSTOMERPARTNER_VARCHAR_ATTRIBUTE` (`attribute_id`),
  KEY `FK_CUSTOMERPARTNER_VARCHAR_ENTITY` (`entity_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE `{$this->getTable('customerpartner_entity')}`
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_ENTITY_STORE` FOREIGN KEY (`store_id`) REFERENCES `{$this->getTable('core_store')}` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_WEBSITE` FOREIGN KEY (`website_id`) REFERENCES `{$this->getTable('core_website')}` (`website_id`) ON DELETE SET NULL ON UPDATE CASCADE;


ALTER TABLE `{$this->getTable('customerpartner_entity_datetime')}`
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_DATETIME_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_DATETIME_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('customerpartner_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_DATETIME_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$this->getTable('customerpartner_entity_decimal')}`
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_DECIMAL_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_DECIMAL_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('customerpartner_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_DECIMAL_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$this->getTable('customerpartner_entity_int')}`
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_INT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_INT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('customerpartner_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_INT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$this->getTable('customerpartner_entity_text')}`
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_TEXT_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_TEXT_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('customerpartner_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_TEXT_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `{$this->getTable('customerpartner_entity_varchar')}`
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_VARCHAR_ATTRIBUTE` FOREIGN KEY (`attribute_id`) REFERENCES `{$this->getTable('eav_attribute')}` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_VARCHAR_ENTITY` FOREIGN KEY (`entity_id`) REFERENCES `{$this->getTable('customerpartner_entity')}` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_CUSTOMERPARTNER_VARCHAR_ENTITY_TYPE` FOREIGN KEY (`entity_type_id`) REFERENCES `{$this->getTable('eav_entity_type')}` (`entity_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;
	");
$installer->installEntities();

$installer->endSetup();
