<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Campaign_0002 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute('alter table `campaigns` add `type` int(1)');
    $this->db->execute('alter table `campaigns` add index (`type`)');
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