<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class RecipientStatus_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipient_statuses` change `is_opted_out` `is_unsubscribed` int(1)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipient_statuses` change `is_unsubscribed` `is_opted_out` int(1)');
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}