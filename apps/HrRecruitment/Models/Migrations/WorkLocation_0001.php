<?php

namespace Hubleto\App\Community\HrRecruitment\Models\Migrations;

use Hubleto\Framework\Migration;

class WorkLocation_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_recruitment_work_locations` (
      `id` int(8) primary key auto_increment,
      `name` varchar(255) NOT NULL,
      `description` text,
      INDEX `name` (`name`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('DROP TABLE IF EXISTS `hr_recruitment_work_locations`');
  }

  public function upgradeForeignKeys(): void {}

  public function downgradeForeignKeys(): void {}
}