<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class Product_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `products`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `products` (
 `id` int(8) primary key auto_increment,
 `ean` varchar(255) ,
 `name` varchar(255) ,
 `id_group` int(8) NULL default NULL,
 `id_category` int(8) NULL default NULL,
 `type` int(255) ,
 `invoicing_policy` int(255) ,
 `is_on_sale` int(1) ,
 `image_1` varchar(255) ,
 `image_2` varchar(255) ,
 `image_3` varchar(255) ,
 `image_4` varchar(255) ,
 `image_5` varchar(255) ,
 `description` text ,
 `notes` text ,
 `sales_price` decimal(14, 4) ,
 `unit` varchar(255) ,
 `margin` decimal(14, 4) ,
 `vat` decimal(14, 4) ,
 `qr_code_data` varchar(255) ,
 `is_single_order_possible` int(1) ,
 `package_unit` varchar(255) ,
 `package_amount` decimal(14, 4) ,
 `package_length` decimal(14, 4) ,
 `package_width` decimal(14, 4) ,
 `package_height` decimal(14, 4) ,
 `package_volume` decimal(14, 4) ,
 `package_mass` decimal(14, 4) ,
 `package_discount` decimal(14, 4) ,
 `package_description` text ,
 `sale_ended` date ,
 `show_price` int(1) ,
 `price_after_reweight` int(1) ,
 `needs_reordering` int(1) ,
 `storage_rules` text ,
 index `id` (`id`),
 index `id_group` (`id_group`),
 index `id_category` (`id_category`),
 index `type` (`type`),
 index `invoicing_policy` (`invoicing_policy`),
 index `is_on_sale` (`is_on_sale`),
 index `is_single_order_possible` (`is_single_order_possible`),
 index `sale_ended` (`sale_ended`),
 index `show_price` (`show_price`),
 index `price_after_reweight` (`price_after_reweight`),
 index `needs_reordering` (`needs_reordering`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `products`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `products`
          ADD CONSTRAINT `fk_ccc7adf173b50aa3bebb3d2d4a89aca4`
          FOREIGN KEY (`id_group`)
          REFERENCES `product_groups` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `products`
          ADD CONSTRAINT `fk_b45b1d54715a0fb69a86b8942e87c8aa`
          FOREIGN KEY (`id_category`)
          REFERENCES `product_categories` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `products`
          DROP FOREIGN KEY `fk_ccc7adf173b50aa3bebb3d2d4a89aca4`; ALTER TABLE `products`
          DROP FOREIGN KEY `fk_b45b1d54715a0fb69a86b8942e87c8aa`;");
  }
}