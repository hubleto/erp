<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Order_0004 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `orders` add `date_expiration` date');
    $this->db->execute('alter table `orders` add index(`date_expiration`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `orders` drop `date_expiration`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}