<?php

namespace Hubleto\App\Community\Mail\Models\Migrations;

use Hubleto\Framework\Migration;

class Attachment_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `mails_attachments` add `size` int(20)");
    $this->db->execute("alter table `mails_attachments` add index(`size`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `mails_attachments` drop `size`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}