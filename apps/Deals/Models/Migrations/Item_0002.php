<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class Item_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `deal_items` add `unit_price` double(20,4)');
    $this->db->execute('alter table `deal_items` add `title` varchar(255)');
    $this->db->execute('alter table `deal_items` add `position` int(8)');
    $this->db->execute('alter table `deal_items` add index(`position`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `deals` drop `unit_price`');
    $this->db->execute('alter table `deals` drop `title`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}