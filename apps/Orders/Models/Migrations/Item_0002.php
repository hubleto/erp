<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Item_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `orders_items` change `amount` `amount` decimal(14,4)');
  }

  public function downgradeSchema(): void
  {
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}