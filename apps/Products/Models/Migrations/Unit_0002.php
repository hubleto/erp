<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class Unit_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `product_units`
      DROP INDEX `category`,
      DROP COLUMN `category`;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `product_units`
      ADD COLUMN `category` int(255) NULL DEFAULT NULL,
      ADD INDEX `category` (`category`);");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
