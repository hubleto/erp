<?php

namespace Hubleto\App\Community\Warehouses\Models\Migrations;

use Hubleto\Framework\Migration;

class Warehouse_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `warehouses` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `id_type` int(8) NULL default NULL,
 `address` varchar(255) ,
 `address_plus_code` varchar(255) ,
 `contact_person` varchar(255) ,
 `contact_email` varchar(255) ,
 `contact_phone` varchar(255) ,
 `lng` decimal(14, 4) ,
 `lat` decimal(14, 4) ,
 `description` text ,
 `capacity` decimal(14, 4) ,
 `capacity_unit` varchar(255) ,
 `current_stock_status` decimal(14, 4) ,
 `operational_status` int(255) ,
 `photo_1` varchar(255) ,
 `photo_2` varchar(255) ,
 `photo_3` varchar(255) ,
 `id_operation_manager` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_type` (`id_type`),
 index `operational_status` (`operational_status`),
 index `id_operation_manager` (`id_operation_manager`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `warehouses`
          ADD CONSTRAINT `fk_a1c2ef05c1697884e0bf4ae4837e4ebe`
          FOREIGN KEY (`id_type`)
          REFERENCES `warehouses_types` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `warehouses`
          ADD CONSTRAINT `fk_6fde5299a150a7ff4ad39dfbacc6ee87`
          FOREIGN KEY (`id_operation_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `warehouses`
          DROP FOREIGN KEY `fk_a1c2ef05c1697884e0bf4ae4837e4ebe`; ALTER TABLE `warehouses`
          DROP FOREIGN KEY `fk_6fde5299a150a7ff4ad39dfbacc6ee87`;");
  }
}