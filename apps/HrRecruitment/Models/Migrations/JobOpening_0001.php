<?php

namespace Hubleto\App\Community\HrRecruitment\Models\Migrations;

use Hubleto\Framework\Migration;

class JobOpening_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_job_openings` (
      `id` int(8) primary key auto_increment,
      `title` varchar(255),
      `department` varchar(255),
      `location` varchar(255),
      `employment_type` varchar(255),
      `status` varchar(255),
      `positions` int(8),
      `date_opened` date,
      `date_closed` date,
      `id_hiring_manager` int(8) NULL DEFAULT NULL,
      `description` text,
      INDEX `status` (`status`),
      INDEX `date_opened` (`date_opened`),
      INDEX `id_hiring_manager` (`id_hiring_manager`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_job_openings`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_job_openings` ADD CONSTRAINT `fk__hr_job_openings__id_hiring_manager` FOREIGN KEY (`id_hiring_manager`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_job_openings` DROP FOREIGN KEY `fk__hr_job_openings__id_hiring_manager`");
  }
}