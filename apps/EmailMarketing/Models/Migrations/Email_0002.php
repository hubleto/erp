<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Email_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_emails` add `title` varchar(255)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_emails` drop `title`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}