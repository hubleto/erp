<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Payment_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders_payments`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `orders_payments` (
 `id` int(8) primary key auto_increment,
 `id_order` int(8) NULL default NULL,
 `title` varchar(255) ,
 `date_due` date ,
 `unit_price` decimal(14, 4) ,
 `amount` decimal(14, 4) ,
 `discount` decimal(14, 4) ,
 `vat` decimal(14, 4) ,
 `notes` text ,
 `id_invoice_item` int(8) NULL default NULL,
 `id_owner` int(8) NULL default NULL,
 `id_manager` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_order` (`id_order`),
 index `date_due` (`date_due`),
 index `id_invoice_item` (`id_invoice_item`),
 index `id_owner` (`id_owner`),
 index `id_manager` (`id_manager`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders_payments`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders_payments`
          ADD CONSTRAINT `fk_3a41246c1117214529b04642f842b4d2`
          FOREIGN KEY (`id_order`)
          REFERENCES `orders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_payments`
          ADD CONSTRAINT `fk_0312dd1ba34343ced99f5b4be2ba027d`
          FOREIGN KEY (`id_invoice_item`)
          REFERENCES `invoice_items` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_payments`
          ADD CONSTRAINT `fk_40fbde0a2506e4ad62a5ca0b12fce693`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_payments`
          ADD CONSTRAINT `fk_fabd54d234c116e6ef644d1212652989`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders_payments`
          DROP FOREIGN KEY `fk_3a41246c1117214529b04642f842b4d2`; ALTER TABLE `orders_payments`
          DROP FOREIGN KEY `fk_0312dd1ba34343ced99f5b4be2ba027d`; ALTER TABLE `orders_payments`
          DROP FOREIGN KEY `fk_40fbde0a2506e4ad62a5ca0b12fce693`; ALTER TABLE `orders_payments`
          DROP FOREIGN KEY `fk_fabd54d234c116e6ef644d1212652989`;");
  }
}