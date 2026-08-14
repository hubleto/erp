<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Recipient_0005 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipients` add `notes` text');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipients` drop `notes`');
  }

  public function upgradeForeignKeys(): void
  {
    //
  }

  public function downgradeForeignKeys(): void
  {
    //
  }
}