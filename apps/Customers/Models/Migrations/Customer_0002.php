<?php

namespace Hubleto\App\Community\Customers\Models\Migrations;

use Hubleto\Framework\Migration;

class Customer_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `customers` change `customer_id` `company_id` varchar(255)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `customers` change `company_id` `customer_id` varchar(255)");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}