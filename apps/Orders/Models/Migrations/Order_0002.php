<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Order_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `orders` add `description_before` text');
    $this->db->execute('alter table `orders` add `description_after` text');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `orders` drop `description_before`');
    $this->db->execute('alter table `orders` drop `description_after`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}