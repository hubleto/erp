<?php

namespace Hubleto\App\Community\Warehouses\Models\Migrations;

use Hubleto\Framework\Migration;

class Transaction_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses_transactions`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `warehouses_transactions` (
 `id` int(8) primary key auto_increment,
 `uid` varchar(255) ,
 `type` int(255) ,
 `id_supplier` int(8) NULL default NULL,
 `supplier_invoice_number` varchar(255) ,
 `supplier_order_number` varchar(255) ,
 `batch_number` varchar(255) ,
 `serial_number` varchar(255) ,
 `id_location_old` int(8) NULL default NULL,
 `id_location_new` int(8) NULL default NULL,
 `document_1` varchar(255) ,
 `document_2` varchar(255) ,
 `document_3` varchar(255) ,
 `notes` text ,
 `created_on` datetime ,
 `id_created_by` int(8) NULL default NULL,
 index `id` (`id`),
 INDEX `uid` (`uid`),
 index `type` (`type`),
 index `id_supplier` (`id_supplier`),
 index `id_location_old` (`id_location_old`),
 index `id_location_new` (`id_location_new`),
 index `created_on` (`created_on`),
 index `id_created_by` (`id_created_by`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses_transactions`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `warehouses_transactions`
          ADD CONSTRAINT `fk_f89fe5f5b253cd932676c5448175b2a4`
          FOREIGN KEY (`id_supplier`)
          REFERENCES `suppliers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `warehouses_transactions`
          ADD CONSTRAINT `fk_2169b721240e806da3b130f7c3bb7100`
          FOREIGN KEY (`id_location_old`)
          REFERENCES `warehouses_locations` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `warehouses_transactions`
          ADD CONSTRAINT `fk_ad7c14cf73267e730bd921d4af9ee5b9`
          FOREIGN KEY (`id_location_new`)
          REFERENCES `warehouses_locations` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `warehouses_transactions`
          ADD CONSTRAINT `fk_e79f01a328370bf067872008a3de2eea`
          FOREIGN KEY (`id_created_by`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `warehouses_transactions`
          DROP FOREIGN KEY `fk_f89fe5f5b253cd932676c5448175b2a4`; ALTER TABLE `warehouses_transactions`
          DROP FOREIGN KEY `fk_2169b721240e806da3b130f7c3bb7100`; ALTER TABLE `warehouses_transactions`
          DROP FOREIGN KEY `fk_ad7c14cf73267e730bd921d4af9ee5b9`; ALTER TABLE `warehouses_transactions`
          DROP FOREIGN KEY `fk_e79f01a328370bf067872008a3de2eea`;");
  }
}