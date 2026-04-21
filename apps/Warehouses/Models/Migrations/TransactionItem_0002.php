<?php

namespace Hubleto\App\Community\Warehouses\Models\Migrations;

use Hubleto\Framework\Migration;

class TransactionItem_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `warehouses_transactions_items` change `purchase_price` `unit_price` double(14,4)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `warehouses_transactions_items` change `unit_price` `purchase_price` double(14,4)");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}