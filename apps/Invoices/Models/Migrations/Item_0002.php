<?php

namespace Hubleto\App\Community\Invoices\Models\Migrations;

use Hubleto\Framework\Migration;

class Item_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `invoice_items` add `attachment_1` varchar(255)');
    $this->db->execute('alter table `invoice_items` add `attachment_2` varchar(255)');
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