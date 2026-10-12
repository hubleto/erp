<?php

namespace Hubleto\App\Community\PriceList\Models\Migrations;

class Resolution_0001 extends \Hubleto\Framework\Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_resolutions`;
      create table `price_lists_resolutions` (
      `id` int(8) primary key auto_increment,
      `model` varchar(255) ,
      `record_id` int(255) ,
      `parent_model` varchar(255) ,
      `parent_record_id` int(255) ,
      `id_price_list` int(8) NULL default NULL,
      `id_item` int(8) NULL default NULL,
      `id_product` int(8) NULL default NULL,
      `qty` decimal(14, 4) ,
      `base_price` decimal(14, 4) ,
      `unit_price` decimal(14, 4) ,
      `discount_percent` decimal(14, 4) ,
      `item_unit_price` decimal(14, 4) ,
      `deviates` int(1) ,
      `trace` text ,
      `date_time` datetime ,
      index `id` (`id`),
      index `record_id` (`record_id`),
      index `parent_record_id` (`parent_record_id`),
      index `id_price_list` (`id_price_list`),
      index `id_item` (`id_item`),
      index `id_product` (`id_product`),
      index `deviates` (`deviates`),
      index `date_time` (`date_time`)
      ) ENGINE = InnoDB;
      set foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_resolutions`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_resolutions` ADD CONSTRAINT `fk_26d22126ab425ad6035b49eda46fe91b` FOREIGN KEY (`id_price_list`) REFERENCES `price_lists` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_resolutions` ADD CONSTRAINT `fk_ae80f0b907af5babd5614f4bc44289e2` FOREIGN KEY (`id_item`) REFERENCES `price_lists_items` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_resolutions` ADD CONSTRAINT `fk_ced4c2dc176408fc1360976000658d31` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_resolutions` DROP FOREIGN KEY `fk_26d22126ab425ad6035b49eda46fe91b`;
      ALTER TABLE `price_lists_resolutions` DROP FOREIGN KEY `fk_ae80f0b907af5babd5614f4bc44289e2`;
      ALTER TABLE `price_lists_resolutions` DROP FOREIGN KEY `fk_ced4c2dc176408fc1360976000658d31`;
    ");
  }
}
