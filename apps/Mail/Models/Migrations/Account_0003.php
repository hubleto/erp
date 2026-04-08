<?php

namespace Hubleto\App\Community\Mail\Models\Migrations;

use Hubleto\Framework\Migration;

class Account_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `mails_accounts` change `max_attachment_size` `max_attachment_size` double(14,4)");
    $this->db->execute("alter table `mails_accounts` drop index `max_attachment_size`");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `mails_accounts` change `max_attachment_size` `max_attachment_size` int(8)");
    $this->db->execute("alter table `mails_accounts` add index(`max_attachment_size`)");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}