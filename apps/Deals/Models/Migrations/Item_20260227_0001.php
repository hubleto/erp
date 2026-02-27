<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class Item_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deal_items`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `deal_items` (
 `id` int(8) primary key auto_increment,
 `id_deal` int(8) NULL default NULL,
 `id_product` int(8) NULL default NULL,
 `order` int(255) ,
 `description` text ,
 `sales_price` decimal(14, 4) ,
 `amount` decimal(14, 4) ,
 `vat` decimal(14, 4) ,
 `discount` decimal(14, 4) ,
 `price_excl_vat` decimal(14, 4) ,
 `price_incl_vat` decimal(14, 4) ,
 index `id` (`id`),
 index `id_deal` (`id_deal`),
 index `id_product` (`id_product`),
 index `order` (`order`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deal_items`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deal_items`
          ADD CONSTRAINT `fk_06347452ab53bd381a7a77059733abbf`
          FOREIGN KEY (`id_deal`)
          REFERENCES `deals` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deal_items`
          ADD CONSTRAINT `fk_c04ec1e22aed2672d1fb0c0a16dde95a`
          FOREIGN KEY (`id_product`)
          REFERENCES `products` (`id`)
          ON DELETE SET NULL
          ON UPDATE CASCADE;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deal_items`
          DROP FOREIGN KEY `fk_06347452ab53bd381a7a77059733abbf`; ALTER TABLE `deal_items`
          DROP FOREIGN KEY `fk_c04ec1e22aed2672d1fb0c0a16dde95a`;");
  }
}