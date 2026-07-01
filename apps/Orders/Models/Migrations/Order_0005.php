<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Order_0005 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `orders` add `date_next_invoice_expected` date');
    $this->db->execute('alter table `orders` add index(`date_next_invoice_expected`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `orders` drop `date_next_invoice_expected`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}