<?php

namespace Hubleto\App\Community\HrLeave\Models\Migrations;

use Hubleto\Framework\Migration;

class LeaveRequest_0003 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("UPDATE `hr_leave_requests` r
      JOIN `workflows` w ON w.`id` = (SELECT MIN(w2.`id`) FROM `workflows` w2 WHERE w2.`group` = 'hr_leave')
      LEFT JOIN `workflow_steps` s ON s.`id_workflow` = w.`id` AND s.`tag` = CASE
        WHEN LOWER(r.`status`) LIKE '%approv%' THEN 'hr-leave-approved'
        WHEN LOWER(r.`status`) LIKE '%reject%' OR LOWER(r.`status`) LIKE '%cancel%' THEN 'hr-leave-rejected'
        WHEN LOWER(r.`status`) LIKE '%review%' THEN 'hr-leave-manager-review'
        ELSE 'hr-leave-submitted'
      END
      SET r.`id_workflow` = w.`id`, r.`id_workflow_step` = COALESCE(s.`id`, (SELECT MIN(s2.`id`) FROM `workflow_steps` s2 WHERE s2.`id_workflow` = w.`id`))
      WHERE r.`id_workflow` IS NULL OR r.`id_workflow_step` IS NULL");
    $this->db->execute('ALTER TABLE `hr_leave_requests` DROP INDEX `status`, DROP COLUMN `status`');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('ALTER TABLE `hr_leave_requests` ADD `status` varchar(255) NULL DEFAULT NULL, ADD INDEX `status` (`status`)');
    $this->db->execute("UPDATE `hr_leave_requests` r JOIN `workflow_steps` s ON s.`id` = r.`id_workflow_step` SET r.`status` = CASE s.`tag` WHEN 'hr-leave-approved' THEN 'Approved' WHEN 'hr-leave-rejected' THEN 'Rejected' ELSE 'Pending' END");
  }

  public function upgradeForeignKeys(): void {}

  public function downgradeForeignKeys(): void {}
}