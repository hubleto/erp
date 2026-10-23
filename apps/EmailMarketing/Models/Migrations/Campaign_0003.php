<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Campaign_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_campaigns` add `type` int(8)");
    $this->db->execute("alter table `email_marketing_campaigns` add index (`type`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_campaigns` drop `type`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}