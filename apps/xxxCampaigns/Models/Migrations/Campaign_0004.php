<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Campaign_0004 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns` add `call_script` text');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns` drop `call_script`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}