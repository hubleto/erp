<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Recipient_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('
      alter table `campaigns_recipients`
      add unique index `campaigns__id_campaign__email` (`id_campaign`, `email`)
    ');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipients` drop index `campaigns__id_campaign__email`');
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