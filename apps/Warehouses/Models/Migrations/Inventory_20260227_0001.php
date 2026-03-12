<?php

namespace Hubleto\App\Community\Warehouses\Models\Migrations;

use Hubleto\Framework\Migration;

class Inventory_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses_inventory`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `warehouses_inventory` (
 `id` int(8) primary key auto_increment,
 `id_product` int(8) NULL default NULL,
 `id_location` int(8) NULL default NULL,
 `quantity` decimal(14, 4) ,
 index `id` (`id`),
 index `id_product` (`id_product`),
 index `id_location` (`id_location`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses_inventory`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `warehouses_inventory`
          ADD CONSTRAINT `fk_1ceeb4436a2ebaada21f36c41a556332`
          FOREIGN KEY (`id_product`)
          REFERENCES `products` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `warehouses_inventory`
          ADD CONSTRAINT `fk_eb1efa2f92f6339ca1b656b7312ba72d`
          FOREIGN KEY (`id_location`)
          REFERENCES `warehouses_locations` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `warehouses_inventory`
          DROP FOREIGN KEY `fk_1ceeb4436a2ebaada21f36c41a556332`; ALTER TABLE `warehouses_inventory`
          DROP FOREIGN KEY `fk_eb1efa2f92f6339ca1b656b7312ba72d`;");
  }
}