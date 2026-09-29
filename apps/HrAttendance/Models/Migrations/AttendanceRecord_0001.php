<?php

namespace Hubleto\App\Community\HrAttendance\Models\Migrations;

use Hubleto\Framework\Migration;

class AttendanceRecord_0001 extends Migration
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
      `status` varchar(255),
      `is_approved` int(1),
      `notes` text,
      INDEX `id_user` (`id_user`),
      INDEX `date_worked` (`date_worked`),
      INDEX `status` (`status`),
      INDEX `is_approved` (`is_approved`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_attendance_records`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_attendance_records` ADD CONSTRAINT `fk__hr_attendance_records__id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_attendance_records` DROP FOREIGN KEY `fk__hr_attendance_records__id_user`");
  }
}