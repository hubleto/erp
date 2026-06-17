<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Campaign_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_campaigns` add `shared_with` text");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_campaigns` drop `shared_with`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}