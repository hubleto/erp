<?php

namespace Hubleto\App\Community\PriceLists\Models\Migrations;

class Item_0001 extends \Hubleto\Framework\Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_items`;
      create table `price_lists_items` (
      `id` int(8) primary key auto_increment,
      `id_price_list` int(8) NULL default NULL,
      `id_product` int(8) NULL default NULL,
      `id_category` int(8) NULL default NULL,
      `id_group` int(8) NULL default NULL,
      `id_unit` int(8) NULL default NULL,
      `id_supplier` int(8) NULL default NULL,
      `supplier_product_code` varchar(255) ,
      `id_vat_rate` int(8) NULL default NULL,
      `min_qty` decimal(14, 4) ,
      `max_qty` decimal(14, 4) ,
      `valid_from` date ,
      `valid_to` date ,
      `method` int(255) ,
      `base` int(255) ,
      `id_base_price_list` int(8) NULL default NULL,
      `value` decimal(14, 4) ,
      `extra_fee` decimal(14, 4) ,
      `min_margin_percent` decimal(14, 4) ,
      `min_price` decimal(14, 4) ,
      `max_price` decimal(14, 4) ,
      `priority` int(255) ,
      `is_active` int(1) ,
      `note` text ,
      index `id` (`id`),
      index `id_price_list` (`id_price_list`),
      index `id_product` (`id_product`),
      index `id_category` (`id_category`),
      index `id_group` (`id_group`),
      index `id_unit` (`id_unit`),
      index `id_supplier` (`id_supplier`),
      index `id_vat_rate` (`id_vat_rate`),
      index `valid_from` (`valid_from`),
      index `valid_to` (`valid_to`),
      index `method` (`method`),
      index `base` (`base`),
      index `id_base_price_list` (`id_base_price_list`),
      index `priority` (`priority`),
      index `is_active` (`is_active`)
      ) ENGINE = InnoDB;
      set foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_items`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_items` ADD CONSTRAINT `fk_b35f4fcaf65d66885e16dc1ca5d030d8` FOREIGN KEY (`id_price_list`) REFERENCES `price_lists` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_items` ADD CONSTRAINT `fk_351f5180a4a456ec213c6995158522b0` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_items` ADD CONSTRAINT `fk_abaf67d28b0e4933f8764f29e37a2237` FOREIGN KEY (`id_category`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_items` ADD CONSTRAINT `fk_aedc476213af5ae70bc89b9dbd01f28b` FOREIGN KEY (`id_group`) REFERENCES `product_groups` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_items` ADD CONSTRAINT `fk_b39fc2d9cbcd0aa0ba76f41745dcf124` FOREIGN KEY (`id_unit`) REFERENCES `product_units` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_items` ADD CONSTRAINT `fk_5c116b615d7890fc46e67d29c5daddb3` FOREIGN KEY (`id_supplier`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_items` ADD CONSTRAINT `fk_439054c248b39f5e06bd50cac534b7d5` FOREIGN KEY (`id_vat_rate`) REFERENCES `price_lists_vat_rates` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_items` ADD CONSTRAINT `fk_aacaa810efefa8bb25f8e233e69edc97` FOREIGN KEY (`id_base_price_list`) REFERENCES `price_lists` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_items` DROP FOREIGN KEY `fk_b35f4fcaf65d66885e16dc1ca5d030d8`;
      ALTER TABLE `price_lists_items` DROP FOREIGN KEY `fk_351f5180a4a456ec213c6995158522b0`;
      ALTER TABLE `price_lists_items` DROP FOREIGN KEY `fk_abaf67d28b0e4933f8764f29e37a2237`;
      ALTER TABLE `price_lists_items` DROP FOREIGN KEY `fk_aedc476213af5ae70bc89b9dbd01f28b`;
      ALTER TABLE `price_lists_items` DROP FOREIGN KEY `fk_b39fc2d9cbcd0aa0ba76f41745dcf124`;
      ALTER TABLE `price_lists_items` DROP FOREIGN KEY `fk_a20b1c6af9a40483994aa2c969e922a8`;
      ALTER TABLE `price_lists_items` DROP FOREIGN KEY `fk_5c116b615d7890fc46e67d29c5daddb3`;
      ALTER TABLE `price_lists_items` DROP FOREIGN KEY `fk_439054c248b39f5e06bd50cac534b7d5`;
      ALTER TABLE `price_lists_items` DROP FOREIGN KEY `fk_aacaa810efefa8bb25f8e233e69edc97`;
    ");
  }
}
