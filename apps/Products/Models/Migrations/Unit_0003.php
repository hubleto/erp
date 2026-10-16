<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class Unit_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `product_units`
      ADD COLUMN `tare_weight` decimal(14, 4) NULL DEFAULT NULL;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `product_units`
      DROP COLUMN `tare_weight`;");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
