<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Recipient_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipients` add index `email` (`email`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipients` drop index `email`');
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