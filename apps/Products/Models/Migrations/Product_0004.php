<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class Product_0004 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `products`
      DROP COLUMN `package_length`,
      DROP COLUMN `package_width`,
      DROP COLUMN `package_height`,
      DROP COLUMN `package_volume`,
      DROP COLUMN `package_mass`,
      DROP COLUMN `package_discount`,
      DROP COLUMN `package_description`,
      ADD COLUMN `base_measure` int(8) NULL DEFAULT 1,
      ADD COLUMN `base_weight` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `base_length` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `base_width` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `base_height` decimal(14, 4) NULL DEFAULT NULL;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `products`
      DROP COLUMN `base_measure`,
      DROP COLUMN `base_weight`,
      DROP COLUMN `base_length`,
      DROP COLUMN `base_width`,
      DROP COLUMN `base_height`,
      ADD COLUMN `package_length` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `package_width` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `package_height` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `package_volume` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `package_mass` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `package_discount` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `package_description` text NULL DEFAULT NULL;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `products` DROP FOREIGN KEY `fk_products_id_unit`;");
    $this->db->execute("ALTER TABLE `products` DROP INDEX `id_unit`, DROP COLUMN `id_unit`;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `products`
      ADD COLUMN `id_unit` int(8) NULL DEFAULT NULL,
      ADD INDEX `id_unit` (`id_unit`);");
    $this->db->execute("ALTER TABLE `products`
      ADD CONSTRAINT `fk_products_id_unit` FOREIGN KEY (`id_unit`) REFERENCES `product_units` (`id`);");
  }
}
