<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class Product_0005 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `products`
      ADD COLUMN `is_lot_tracked` int(1) NULL DEFAULT 0;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `products`
      DROP COLUMN `is_lot_tracked`;");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
