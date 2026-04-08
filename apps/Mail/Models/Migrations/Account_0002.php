<?php

namespace Hubleto\App\Community\Mail\Models\Migrations;

use Hubleto\Framework\Migration;

class Account_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `mails_accounts` add `max_attachment_size` int(20)");
    $this->db->execute("alter table `mails_accounts` add index(`max_attachment_size`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `mails_accounts` drop `max_attachment_size`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}