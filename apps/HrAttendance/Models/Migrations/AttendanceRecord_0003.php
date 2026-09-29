<?php

namespace Hubleto\App\Community\HrAttendance\Models\Migrations;

use Hubleto\Framework\Migration;

class AttendanceRecord_0003 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("UPDATE `hr_attendance_records` r
      JOIN `workflows` w ON w.`id` = (SELECT MIN(w2.`id`) FROM `workflows` w2 WHERE w2.`group` = 'hr_attendance')
      JOIN `workflow_steps` s ON s.`id_workflow` = w.`id` AND s.`tag` = IF(r.`is_approved` = 1, 'hr-attendance-approved', 'hr-attendance-submitted')
      SET r.`id_workflow` = w.`id`, r.`id_workflow_step` = s.`id`
      WHERE r.`id_workflow` IS NULL OR r.`id_workflow_step` IS NULL");
    $this->db->execute('ALTER TABLE `hr_attendance_records` DROP INDEX `status`, DROP COLUMN `status`');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('ALTER TABLE `hr_attendance_records` ADD `status` varchar(255) NULL DEFAULT NULL, ADD INDEX `status` (`status`)');
    $this->db->execute("UPDATE `hr_attendance_records` r JOIN `workflow_steps` s ON s.`id` = r.`id_workflow_step` SET r.`status` = s.`name`");
  }

  public function upgradeForeignKeys(): void {}

  public function downgradeForeignKeys(): void {}
}