<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class RecipientStatus_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipient_statuses` add unique index `email_unique` (`email`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipient_statuses` drop index `email_unique`');
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}