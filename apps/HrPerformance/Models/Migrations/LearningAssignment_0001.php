<?php

namespace Hubleto\App\Community\HrPerformance\Models\Migrations;

use Hubleto\Framework\Migration;

class LearningAssignment_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_learning_assignments` (
      `id` int(8) primary key auto_increment,
      `id_user` int(8) NULL DEFAULT NULL,
      `id_course` int(8) NULL DEFAULT NULL,
      `date_assigned` date,
      `date_due` date,
      `date_completed` date,
      `status` varchar(255),
      `notes` text,
      INDEX `id_user` (`id_user`),
      INDEX `id_course` (`id_course`),
      INDEX `date_assigned` (`date_assigned`),
      INDEX `date_due` (`date_due`),
      INDEX `status` (`status`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_learning_assignments`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_learning_assignments` ADD CONSTRAINT `fk__hr_learning_assignments__id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    $this->db->execute("ALTER TABLE `hr_learning_assignments` ADD CONSTRAINT `fk__hr_learning_assignments__id_course` FOREIGN KEY (`id_course`) REFERENCES `hr_courses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_learning_assignments` DROP FOREIGN KEY `fk__hr_learning_assignments__id_user`");
    $this->db->execute("ALTER TABLE `hr_learning_assignments` DROP FOREIGN KEY `fk__hr_learning_assignments__id_course`");
  }
}