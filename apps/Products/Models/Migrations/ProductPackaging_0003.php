<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

// Packaging levels are part of the product, so they go when the product goes. Products with
// history (orders, stock, lots) stay protected by those tables' own keys.
class ProductPackaging_0003 extends Migration
{

  public function upgradeSchema(): void
  {
  }

  public function downgradeSchema(): void
  {
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `product_packagings`
      DROP FOREIGN KEY `fk_product_packagings_id_product`;
    ALTER TABLE `product_packagings`
      ADD CONSTRAINT `fk_product_packagings_id_product`
      FOREIGN KEY (`id_product`)
      REFERENCES `products` (`id`)
      ON DELETE CASCADE
      ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `product_packagings`
      DROP FOREIGN KEY `fk_product_packagings_id_product`;
    ALTER TABLE `product_packagings`
      ADD CONSTRAINT `fk_product_packagings_id_product`
      FOREIGN KEY (`id_product`)
      REFERENCES `products` (`id`)
      ON DELETE RESTRICT
      ON UPDATE RESTRICT;");
  }
}
