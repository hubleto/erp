<?php

namespace Hubleto\App\Community\Warehouses\Models\Migrations;

use Hubleto\Framework\Migration;

class Inventory_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `warehouses_inventory`;
      create table `warehouses_inventory` (
        `id` int(8) primary key auto_increment,
        `id_product` int(8) NULL default NULL,
        `id_location` int(8) NULL default NULL,
        `quantity` decimal(14, 4) ,
        index `id` (`id`),
        index `id_product` (`id_product`),
        index `id_location` (`id_location`),
        UNIQUE `id_product__id_location` (`id_product`, `id_location`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `warehouses_inventory`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `warehouses_inventory`
      ADD CONSTRAINT `fk__warehouses_inventory__id_product`
      FOREIGN KEY (`id_product`)
      REFERENCES `products` (`id`)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT
    ");
    $this->db->execute("
      ALTER TABLE `warehouses_inventory`
      ADD CONSTRAINT `fk__warehouses_inventory__id_location`
      FOREIGN KEY (`id_location`)
      REFERENCES `warehouses_locations` (`id`)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `warehouses_inventory` DROP FOREIGN KEY `fk__warehouses_inventory__id_product`;
      ALTER TABLE `warehouses_inventory` DROP FOREIGN KEY `fk__warehouses_inventory__id_location`;
    ");
  }
}