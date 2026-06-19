<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Recipient_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_email_recipients` rename `email_marketing_recipients`");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_recipients` rename `email_marketing_email_recipients`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}