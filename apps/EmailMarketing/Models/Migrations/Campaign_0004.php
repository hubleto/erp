<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Campaign_0004 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_campaigns` add `mail_body_template` text");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_campaigns` drop `mail_body_template`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}