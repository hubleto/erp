<?php

namespace Hubleto\App\Community\Warehouses\Models\Migrations;

use Hubleto\Framework\Migration;

class Location_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses_locations`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `warehouses_locations` (
 `id` int(8) primary key auto_increment,
 `id_warehouse` int(8) NULL default NULL,
 `code` varchar(255) ,
 `id_type` int(8) NULL default NULL,
 `description` text ,
 `capacity` decimal(14, 4) ,
 `current_stock_status` decimal(14, 4) ,
 `operational_status` int(255) ,
 `placement` text ,
 `photo_1` varchar(255) ,
 `photo_2` varchar(255) ,
 `photo_3` varchar(255) ,
 `id_operation_manager` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_warehouse` (`id_warehouse`),
 index `id_type` (`id_type`),
 index `operational_status` (`operational_status`),
 index `id_operation_manager` (`id_operation_manager`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses_locations`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `warehouses_locations`
          ADD CONSTRAINT `fk_e1b84d6d6ea5dcb19e266b51d9978554`
          FOREIGN KEY (`id_warehouse`)
          REFERENCES `warehouses` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `warehouses_locations`
          ADD CONSTRAINT `fk_ccaba93e7a14a828bbf5d708d491331e`
          FOREIGN KEY (`id_type`)
          REFERENCES `warehouses_locations_types` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `warehouses_locations`
          ADD CONSTRAINT `fk_3161a1fba04d2f001b288c938079343b`
          FOREIGN KEY (`id_operation_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `warehouses_locations`
          DROP FOREIGN KEY `fk_e1b84d6d6ea5dcb19e266b51d9978554`; ALTER TABLE `warehouses_locations`
          DROP FOREIGN KEY `fk_ccaba93e7a14a828bbf5d708d491331e`; ALTER TABLE `warehouses_locations`
          DROP FOREIGN KEY `fk_3161a1fba04d2f001b288c938079343b`;");
  }
}