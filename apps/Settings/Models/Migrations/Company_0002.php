<?php

namespace Hubleto\App\Community\Settings\Models\Migrations;

use Hubleto\Framework\Migration;

class Company_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `companies` change `registration_id` `company_id` varchar(255)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `companies` change `company_id` `registration_id` varchar(255)");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}