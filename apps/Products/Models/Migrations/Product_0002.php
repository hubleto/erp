<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class Product_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `products`
      ADD COLUMN `id_unit` int(8) NULL DEFAULT NULL AFTER `unit`,
      ADD INDEX `id_unit` (`id_unit`);");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `products`
      DROP INDEX `id_unit`,
      DROP COLUMN `id_unit`;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `products`
      ADD CONSTRAINT `fk_products_id_unit`
      FOREIGN KEY (`id_unit`)
      REFERENCES `product_units` (`id`)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `products`
      DROP FOREIGN KEY `fk_products_id_unit`;");
  }
}
