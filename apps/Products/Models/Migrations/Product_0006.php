<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class Product_0006 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `products`
      ADD COLUMN `base_net_weight` decimal(14, 4) NULL DEFAULT NULL;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `products`
      DROP COLUMN `base_net_weight`;");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
