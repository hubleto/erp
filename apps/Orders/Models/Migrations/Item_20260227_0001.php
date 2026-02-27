<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Item_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders_items`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `orders_items` (
 `id` int(8) primary key auto_increment,
 `id_order` int(8) NULL default NULL,
 `title` varchar(255) ,
 `id_product` int(8) NULL default NULL,
 `unit_price` decimal(14, 4) ,
 `amount` decimal(14, 4) ,
 `discount` decimal(14, 4) ,
 `vat` decimal(14, 4) ,
 `price_excl_vat` decimal(14, 4) ,
 `price_incl_vat` decimal(14, 4) ,
 `date_due` date ,
 `notes` text ,
 `id_invoice_item` int(8) NULL default NULL,
 `id_owner` int(8) NULL default NULL,
 `id_manager` int(8) NULL default NULL,
 `position` int(255) ,
 index `id` (`id`),
 index `id_order` (`id_order`),
 index `id_product` (`id_product`),
 index `date_due` (`date_due`),
 index `id_invoice_item` (`id_invoice_item`),
 index `id_owner` (`id_owner`),
 index `id_manager` (`id_manager`),
 index `position` (`position`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders_items`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders_items`
          ADD CONSTRAINT `fk_e7ab9fff202b2052cfa00bd5c950c51c`
          FOREIGN KEY (`id_order`)
          REFERENCES `orders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_items`
          ADD CONSTRAINT `fk_8189335d844ee881a47d0b7aab5d10a8`
          FOREIGN KEY (`id_product`)
          REFERENCES `products` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_items`
          ADD CONSTRAINT `fk_c6c0c7c0839f73646d16d5b72a2468b3`
          FOREIGN KEY (`id_invoice_item`)
          REFERENCES `invoice_items` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_items`
          ADD CONSTRAINT `fk_462d1d56c9c953b50e51d9fe158b9a59`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_items`
          ADD CONSTRAINT `fk_83fe1ad7280ab8192c84cc769ecfc9ca`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders_items`
          DROP FOREIGN KEY `fk_e7ab9fff202b2052cfa00bd5c950c51c`; ALTER TABLE `orders_items`
          DROP FOREIGN KEY `fk_8189335d844ee881a47d0b7aab5d10a8`; ALTER TABLE `orders_items`
          DROP FOREIGN KEY `fk_c6c0c7c0839f73646d16d5b72a2468b3`; ALTER TABLE `orders_items`
          DROP FOREIGN KEY `fk_462d1d56c9c953b50e51d9fe158b9a59`; ALTER TABLE `orders_items`
          DROP FOREIGN KEY `fk_83fe1ad7280ab8192c84cc769ecfc9ca`;");
  }
}