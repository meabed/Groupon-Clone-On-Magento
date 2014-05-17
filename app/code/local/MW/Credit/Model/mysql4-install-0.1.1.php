<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('easy_banner')};
CREATE TABLE {$this->getTable('easy_banner')} (
  `banner_id` int(11) unsigned NOT NULL auto_increment,
  `identifier` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `show_title` smallint(6) NOT NULL default '0',
  `content` text NULL default '',
  `width` int(11) unsigned NULL,
  `height` int(11) unsigned NULL,
  `delay` int(11) unsigned NULL,
  `status` smallint(6) NOT NULL default '0',
  `active_from` datetime NULL,
  `active_to` datetime NULL,
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`banner_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('easy_banner_item')};
CREATE TABLE {$this->getTable('easy_banner_item')} (
  `banner_item_id` int(11) unsigned NOT NULL auto_increment,
  `banner_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `image_url` varchar(512) NOT NULL default '',
  `thumb_image` varchar(255) NOT NULL default '',
  `thumb_image_url` varchar(512) NOT NULL default '',
  `content` text NULL default '',
  `link_url` varchar(512) NOT NULL default '#',
  `status` smallint(6) NOT NULL default '0',
  `created_time` datetime NULL,
  `update_time` datetime NULL,
  PRIMARY KEY (`banner_item_id`),
  CONSTRAINT `FK_EASY_BANNER_ITEM` FOREIGN KEY (`banner_id`) REFERENCES `{$this->getTable('easy_banner')}` (`banner_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");

$installer->endSetup(); 