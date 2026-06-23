<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Recipient_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_recipients` add `date_added` date");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_recipients` drop `date_added`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}