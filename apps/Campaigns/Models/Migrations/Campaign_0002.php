<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Campaign_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns` add `type` int(1)');
    $this->db->execute('alter table `campaigns` add index (`type`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns` drop `type`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}