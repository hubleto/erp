<?php

namespace Hubleto\App\Community\HrRecruitment\Models\Migrations;

use Hubleto\Framework\Migration;

class OpeningDate_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_recruitment_opening_dates` (
      `id` int(8) primary key auto_increment,
      `name` varchar(255) NOT NULL,
      `opened_on` date NOT NULL,
      INDEX `name` (`name`),
      INDEX `opened_on` (`opened_on`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('DROP TABLE IF EXISTS `hr_recruitment_opening_dates`');
  }

  public function upgradeForeignKeys(): void {}

  public function downgradeForeignKeys(): void {}
}