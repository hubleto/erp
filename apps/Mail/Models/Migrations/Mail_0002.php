<?php

namespace Hubleto\App\Community\Mail\Models\Migrations;

use Hubleto\Framework\Migration;

class Mail_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `mails` add `in_reply_to` varchar(255)");
    $this->db->execute("alter table `mails` add index(`in_reply_to`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `mails` drop `in_reply_to`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}