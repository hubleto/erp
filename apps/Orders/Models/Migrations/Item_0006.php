<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Item_0006 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `orders_items` add `charged_period_start` date');
    $this->db->execute('alter table `orders_items` add index(`charged_period_start`)');

    $this->db->execute('alter table `orders_items` add `charged_period_end` date');
    $this->db->execute('alter table `orders_items` add index(`charged_period_end`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `orders_items` drop `charged_period_start`');
    $this->db->execute('alter table `orders_items` drop `charged_period_end`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}