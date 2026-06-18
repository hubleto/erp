<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

class Lead_0004 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `leads` add `shared_with` text');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `leads` drop `shared_with`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}