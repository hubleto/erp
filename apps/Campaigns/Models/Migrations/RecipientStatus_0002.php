<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class RecipientStatus_0002 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute('alter table `campaigns_recipient_statuses` change `is_opted_out` `is_unsubscribed` int(1)');
  }

  public function uninstallTables(): void
  {
    
  }

  public function installForeignKeys(): void
  {
    
  }

  public function uninstallForeignKeys(): void
  {
    
  }
}