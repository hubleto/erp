<?php

namespace Hubleto\App\Community\HrPerformance\Models\Migrations;

use Hubleto\Framework\Migration;

class Course_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_courses` (
      `id` int(8) primary key auto_increment,
      `name` varchar(255),
      `provider` varchar(255),
      `delivery` varchar(255),
      `duration_hours` int(8),
      `url` varchar(255),
      `is_active` int(1),
      `description` text,
      INDEX `name` (`name`),
      INDEX `is_active` (`is_active`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_courses`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}