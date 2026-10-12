<?php

namespace Hubleto\App\Community\PriceList\Models\Migrations;

class Rule_0001 extends \Hubleto\Framework\Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_rules`;
      create table `price_lists_rules` (
      `id` int(8) primary key auto_increment,
      `name` varchar(255) ,
      `id_price_list` int(8) NULL default NULL,
      `kind` int(255) ,
      `value` decimal(14, 4) ,
      `id_product` int(8) NULL default NULL,
      `id_category` int(8) NULL default NULL,
      `id_group` int(8) NULL default NULL,
      `id_customer` int(8) NULL default NULL,
      `id_customer_tag` int(8) NULL default NULL,
      `min_qty` decimal(14, 4) ,
      `max_qty` decimal(14, 4) ,
      `min_amount` decimal(14, 4) ,
      `max_amount` decimal(14, 4) ,
      `valid_from` date ,
      `valid_to` date ,
      `priority` int(255) ,
      `is_stackable` int(1) ,
      `is_active` int(1) ,
      `note` text ,
      index `id` (`id`),
      index `id_price_list` (`id_price_list`),
      index `kind` (`kind`),
      index `id_product` (`id_product`),
      index `id_category` (`id_category`),
      index `id_group` (`id_group`),
      index `id_customer` (`id_customer`),
      index `id_customer_tag` (`id_customer_tag`),
      index `valid_from` (`valid_from`),
      index `valid_to` (`valid_to`),
      index `priority` (`priority`),
      index `is_stackable` (`is_stackable`),
      index `is_active` (`is_active`)
      ) ENGINE = InnoDB;
      set foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_rules`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_rules` ADD CONSTRAINT `fk_7be9d98037bd05848f598eb3940e96f4` FOREIGN KEY (`id_price_list`) REFERENCES `price_lists` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_rules` ADD CONSTRAINT `fk_4ca1e1995861a7b696671bce240d0ce4` FOREIGN KEY (`id_product`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_rules` ADD CONSTRAINT `fk_ef7d159b724d66bd5ff23e1a65960063` FOREIGN KEY (`id_category`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_rules` ADD CONSTRAINT `fk_2fe30f99051b1633af6d5769fbe246dc` FOREIGN KEY (`id_group`) REFERENCES `product_groups` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_rules` ADD CONSTRAINT `fk_4c5dea8ebd368698fbb916ba29258218` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_rules` ADD CONSTRAINT `fk_0e19db3ab3075192a11fcd3179a8116e` FOREIGN KEY (`id_customer_tag`) REFERENCES `customer_tags` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_rules` DROP FOREIGN KEY `fk_7be9d98037bd05848f598eb3940e96f4`;
      ALTER TABLE `price_lists_rules` DROP FOREIGN KEY `fk_4ca1e1995861a7b696671bce240d0ce4`;
      ALTER TABLE `price_lists_rules` DROP FOREIGN KEY `fk_ef7d159b724d66bd5ff23e1a65960063`;
      ALTER TABLE `price_lists_rules` DROP FOREIGN KEY `fk_2fe30f99051b1633af6d5769fbe246dc`;
      ALTER TABLE `price_lists_rules` DROP FOREIGN KEY `fk_4c5dea8ebd368698fbb916ba29258218`;
      ALTER TABLE `price_lists_rules` DROP FOREIGN KEY `fk_0e19db3ab3075192a11fcd3179a8116e`;
    ");
  }
}
