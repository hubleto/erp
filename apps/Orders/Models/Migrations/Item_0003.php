<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Item_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `orders_items` add `attachment_1` varchar(255)');
    $this->db->execute('alter table `orders_items` add `attachment_2` varchar(255)');
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