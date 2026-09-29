<?php

namespace Hubleto\App\Community\HrRecruitment\Models\Migrations;

use Hubleto\Framework\Migration;

class Application_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_applications` (
      `id` int(8) primary key auto_increment,
      `id_job_opening` int(8) NULL DEFAULT NULL,
      `id_candidate` int(8) NULL DEFAULT NULL,
      `stage` varchar(255),
      `status` varchar(255),
      `date_applied` date,
      `date_decided` date,
      `notes` text,
      INDEX `id_job_opening` (`id_job_opening`),
      INDEX `id_candidate` (`id_candidate`),
      INDEX `stage` (`stage`),
      INDEX `status` (`status`),
      INDEX `date_applied` (`date_applied`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_applications`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_applications` ADD CONSTRAINT `fk__hr_applications__id_job_opening` FOREIGN KEY (`id_job_opening`) REFERENCES `hr_job_openings` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    $this->db->execute("ALTER TABLE `hr_applications` ADD CONSTRAINT `fk__hr_applications__id_candidate` FOREIGN KEY (`id_candidate`) REFERENCES `hr_candidates` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_applications` DROP FOREIGN KEY `fk__hr_applications__id_job_opening`");
    $this->db->execute("ALTER TABLE `hr_applications` DROP FOREIGN KEY `fk__hr_applications__id_candidate`");
  }
}