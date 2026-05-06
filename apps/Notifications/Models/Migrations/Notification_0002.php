<?php

namespace Hubleto\App\Community\Notifications\Models\Migrations;

use Hubleto\Framework\Migration;

class Notification_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `notifications` add `model` varchar(255)");
    $this->db->execute("alter table `notifications` add `record_id` int(8)");
    $this->db->execute("alter table `notifications` add index(`model`)");
    $this->db->execute("alter table `notifications` add index(`record_id`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `notifications` drop index `model`");
    $this->db->execute("alter table `notifications` drop index `record_id`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}