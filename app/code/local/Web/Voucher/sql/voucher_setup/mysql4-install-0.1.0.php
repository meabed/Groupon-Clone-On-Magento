<?php
$installer = $this;

$installer->startSetup();

$installer->run(
    "
    DROP TABLE IF EXISTS {$this->getTable('deal_voucher')};
    CREATE TABLE {$this->getTable('deal_voucher')} (
    `entity_id` int(11) NOT NULL AUTO_INCREMENT,
    `deal_voucher_code` varchar(50) DEFAULT NULL COMMENT 'Order Increment Id',
    `customer_id` int(10) unsigned DEFAULT NULL COMMENT 'Customer Id',
    `store_id` smallint(5) unsigned DEFAULT NULL COMMENT 'Store Id',
    `order_id` int(10) unsigned NOT NULL COMMENT 'Order Id',
    `product_id` int(10) unsigned NOT NULL COMMENT 'Product Id',
    `order_increment_id` varchar(50) DEFAULT NULL COMMENT 'Order Increment Id',
    `is_sent` tinyint(1) NOT NULL DEFAULT '0',
    `pdf_file` varchar(255) DEFAULT NULL COMMENT 'Pdf File',
    `status` varchar(50) NOT NULL,
    `created_at` timestamp NULL DEFAULT NULL COMMENT 'Created At',
    `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Updated At',
    `order_created_at` timestamp NULL DEFAULT NULL COMMENT 'Order Created At',
     PRIMARY KEY (`entity_id`)
     -- UNIQUE KEY `UNQ_DEAL_VOUCHER_ORDER_INCREMENT_ID` (`order_increment_id`)
    ) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
    
  ALTER TABLE `deal_voucher`
  ADD CONSTRAINT `FK_DEAL_VOUCHER_CUSTOMER_ID_CUSTOMER_ENTITY_ENTITY_ID` FOREIGN KEY (`customer_id`) REFERENCES `customer_entity` (`entity_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DEAL_VOUCHER_CORE_STORE_STORE_ID` FOREIGN KEY (`store_id`) REFERENCES `core_store` (`store_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DEAL_VOUCHER_CATALOG_PRODUCT_ENTITY` FOREIGN KEY (`product_id`) REFERENCES `catalog_product_entity` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FK_DEAL_VOUCHER_SALES_FLAT_ORDER_ENTITY_ID` FOREIGN KEY (`order_id`) REFERENCES `sales_flat_order` (`entity_id`) ON DELETE CASCADE ON UPDATE CASCADE;
  "
);

$installer->endSetup();
