<?php

namespace Hubleto\App\Community\HrLeave\Models\Migrations;

use Hubleto\Framework\Migration;

class LeaveType_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_leave_types` (
      `id` int(8) primary key auto_increment,
      `name` varchar(255),
      `annual_entitlement` decimal(14,2),
      `is_paid` int(1),
      `requires_approval` int(1),
      `description` text,
      INDEX `name` (`name`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_leave_types`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}