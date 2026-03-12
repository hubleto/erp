<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class ProductSupplier_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `products_suppliers`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `products_suppliers` (
 `id` int(8) primary key auto_increment,
 `id_product` int(8) NULL default NULL,
 `id_supplier` int(8) NULL default NULL,
 `supplier_product_name` text ,
 `supplier_product_code` text ,
 `purchase_price` decimal(14, 4) ,
 `notes` text ,
 `delivery_time` int(255) ,
 index `id` (`id`),
 index `id_product` (`id_product`),
 index `id_supplier` (`id_supplier`),
 index `delivery_time` (`delivery_time`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `products_suppliers`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `products_suppliers`
          ADD CONSTRAINT `fk_c942d571ee00c3d6175eeea4e4e7340d`
          FOREIGN KEY (`id_product`)
          REFERENCES `products` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `products_suppliers`
          ADD CONSTRAINT `fk_672aea27ee512a87196dfc192da78d60`
          FOREIGN KEY (`id_supplier`)
          REFERENCES `suppliers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `products_suppliers`
          DROP FOREIGN KEY `fk_c942d571ee00c3d6175eeea4e4e7340d`; ALTER TABLE `products_suppliers`
          DROP FOREIGN KEY `fk_672aea27ee512a87196dfc192da78d60`;");
  }
}