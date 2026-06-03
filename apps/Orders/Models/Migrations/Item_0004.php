<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Item_0004 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `orders_items` add `is_chargeable` int(1)');
    $this->db->execute('alter table `orders_items` add index (`is_chargeable`)');
    $this->db->execute('update `orders_items` set `is_chargeable` = 1');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `orders_items` drop `is_chargeable`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}