<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class ProductPackaging_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `product_packagings`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `product_packagings` (
 `id` int(8) primary key auto_increment,
 `id_product` int(8) NULL default NULL,
 `id_unit` int(8) NULL default NULL,
 `qty_per_lower` decimal(14, 4) ,
 `sort` int(8) ,
 index `id` (`id`),
 index `id_product` (`id_product`),
 index `id_unit` (`id_unit`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `product_packagings`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `product_packagings`
      ADD CONSTRAINT `fk_product_packagings_id_product`
      FOREIGN KEY (`id_product`)
      REFERENCES `products` (`id`)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT;
    ALTER TABLE `product_packagings`
      ADD CONSTRAINT `fk_product_packagings_id_unit`
      FOREIGN KEY (`id_unit`)
      REFERENCES `product_units` (`id`)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `product_packagings`
      DROP FOREIGN KEY `fk_product_packagings_id_product`;
    ALTER TABLE `product_packagings`
      DROP FOREIGN KEY `fk_product_packagings_id_unit`;");
  }
}
