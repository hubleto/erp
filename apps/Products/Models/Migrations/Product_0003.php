<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class Product_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `products`
      DROP COLUMN `unit`,
      DROP COLUMN `package_unit`,
      DROP COLUMN `package_amount`;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `products`
      ADD COLUMN `unit` varchar(255) NULL DEFAULT NULL,
      ADD COLUMN `package_unit` varchar(255) NULL DEFAULT NULL,
      ADD COLUMN `package_amount` decimal(14, 4) NULL DEFAULT NULL;");
  }

  public function upgradeForeignKeys(): void
  {

  }

  public function downgradeForeignKeys(): void
  {

  }
}
