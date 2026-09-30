<?php

namespace Hubleto\App\Community\HrAttendance\Models\Migrations;

use Hubleto\Framework\Migration;

class Attendance_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_attendance_records` (
      `id` int(8) primary key auto_increment,
      `id_user` int(8) NULL DEFAULT NULL,
      `date_worked` date,
      `time_in` time,
      `time_out` time,
      `break_minutes` decimal(14,2),
      `is_approved` int(1),
      `notes` text,
      `id_shift` int(8) NULL DEFAULT NULL,
      `id_workflow` int(8) NULL DEFAULT NULL,
      `id_workflow_step` int(8) NULL DEFAULT NULL,
      INDEX `id_user` (`id_user`),
      INDEX `date_worked` (`date_worked`),
      INDEX `is_approved` (`is_approved`),
      INDEX `id_shift` (`id_shift`),
      INDEX `id_workflow` (`id_workflow`),
      INDEX `id_workflow_step` (`id_workflow_step`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_attendance_records`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_attendance_records` ADD CONSTRAINT `fk__hr_attendance_records__id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    $this->db->execute('ALTER TABLE `hr_attendance_records` ADD CONSTRAINT `fk__hr_attendance_records__id_shift` FOREIGN KEY (`id_shift`) REFERENCES `hr_shifts` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
    $this->db->execute('ALTER TABLE `hr_attendance_records` ADD CONSTRAINT `fk__hr_attendance_records__id_workflow` FOREIGN KEY (`id_workflow`) REFERENCES `workflows` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
    $this->db->execute('ALTER TABLE `hr_attendance_records` ADD CONSTRAINT `fk__hr_attendance_records__id_workflow_step` FOREIGN KEY (`id_workflow_step`) REFERENCES `workflow_steps` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_attendance_records` DROP FOREIGN KEY `fk__hr_attendance_records__id_user`");
    $this->db->execute('ALTER TABLE `hr_attendance_records` DROP FOREIGN KEY `fk__hr_attendance_records__id_shift`');
    $this->db->execute('ALTER TABLE `hr_attendance_records` DROP FOREIGN KEY `fk__hr_attendance_records__id_workflow`');
    $this->db->execute('ALTER TABLE `hr_attendance_records` DROP FOREIGN KEY `fk__hr_attendance_records__id_workflow_step`');
  }
}