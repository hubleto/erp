<?php

namespace Hubleto\App\Community\HrRecruitment\Models\Migrations;

use Hubleto\Framework\Migration;

class Interview_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_interviews` (
      `id` int(8) primary key auto_increment,
      `id_application` int(8) NULL DEFAULT NULL,
      `id_interviewer` int(8) NULL DEFAULT NULL,
      `date_start` datetime,
      `date_end` datetime,
      `location` varchar(255),
      `status` varchar(255),
      `feedback` text,
      INDEX `id_application` (`id_application`),
      INDEX `id_interviewer` (`id_interviewer`),
      INDEX `date_start` (`date_start`),
      INDEX `status` (`status`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_interviews`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_interviews` ADD CONSTRAINT `fk__hr_interviews__id_application` FOREIGN KEY (`id_application`) REFERENCES `hr_applications` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    $this->db->execute("ALTER TABLE `hr_interviews` ADD CONSTRAINT `fk__hr_interviews__id_interviewer` FOREIGN KEY (`id_interviewer`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_interviews` DROP FOREIGN KEY `fk__hr_interviews__id_application`");
    $this->db->execute("ALTER TABLE `hr_interviews` DROP FOREIGN KEY `fk__hr_interviews__id_interviewer`");
  }
}