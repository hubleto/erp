<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Click_0002 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute('alter table `campaigns_clicks` add `log` json');
    $this->db->execute('alter table `campaigns_clicks` add `bot_score` int(8)');
    $this->db->execute('alter table `campaigns_clicks` add index `bot_score` (`bot_score`)');
  }

  public function uninstallTables(): void
  {
    $this->db->execute('alter table `campaigns_clicks` drop `log`');
    $this->db->execute('alter table `campaigns_clicks` drop `bot_score`');
  }

  public function installForeignKeys(): void
  {
    //
  }

  public function uninstallForeignKeys(): void
  {
    //
  }
}