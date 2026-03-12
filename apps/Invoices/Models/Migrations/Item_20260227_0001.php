<?php

namespace Hubleto\App\Community\Invoices\Models\Migrations;

use Hubleto\Framework\Migration;

class Item_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `invoice_items`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `invoice_items` (
 `id` int(8) primary key auto_increment,
 `id_invoice` int(8) NULL default NULL,
 `id_customer` int(8) NULL default NULL,
 `id_order` int(8) NULL default NULL,
 `id_order_item` int(8) NULL default NULL,
 `item` varchar(255) ,
 `unit_price` decimal(14, 4) ,
 `amount` decimal(14, 4) ,
 `discount` decimal(14, 4) ,
 `vat` decimal(14, 4) ,
 `price_excl_vat` decimal(14, 4) ,
 `price_vat` decimal(14, 4) ,
 `price_incl_vat` decimal(14, 4) ,
 index `id` (`id`),
 index `id_invoice` (`id_invoice`),
 index `id_customer` (`id_customer`),
 index `id_order` (`id_order`),
 index `id_order_item` (`id_order_item`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `invoice_items`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `invoice_items`
          ADD CONSTRAINT `fk_1b00ad76e80168594c31be01338d4b44`
          FOREIGN KEY (`id_invoice`)
          REFERENCES `invoices` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoice_items`
          ADD CONSTRAINT `fk_94b46310a475f59f2552e54df62f7b01`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoice_items`
          ADD CONSTRAINT `fk_8a5dbd71fda9248d8ece4f8146fc29af`
          FOREIGN KEY (`id_order`)
          REFERENCES `orders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoice_items`
          ADD CONSTRAINT `fk_2fcd97e20a61260fffe3a5fad3d2e2a2`
          FOREIGN KEY (`id_order_item`)
          REFERENCES `orders_items` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `invoice_items`
          DROP FOREIGN KEY `fk_1b00ad76e80168594c31be01338d4b44`; ALTER TABLE `invoice_items`
          DROP FOREIGN KEY `fk_94b46310a475f59f2552e54df62f7b01`; ALTER TABLE `invoice_items`
          DROP FOREIGN KEY `fk_8a5dbd71fda9248d8ece4f8146fc29af`; ALTER TABLE `invoice_items`
          DROP FOREIGN KEY `fk_2fcd97e20a61260fffe3a5fad3d2e2a2`;");
  }
}