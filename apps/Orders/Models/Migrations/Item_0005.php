<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Item_0005 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `orders_items` add `date_delivery` date');
    $this->db->execute('alter table `orders_items` add index(`date_delivery`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `orders_items` drop `date_delivery`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}