<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class ProductPackaging_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `product_packagings`
      ADD COLUMN `length` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `width` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `height` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `weight` decimal(14, 4) NULL DEFAULT NULL,
      ADD COLUMN `description` text NULL DEFAULT NULL;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `product_packagings`
      DROP COLUMN `length`,
      DROP COLUMN `width`,
      DROP COLUMN `height`,
      DROP COLUMN `weight`,
      DROP COLUMN `description`;");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
