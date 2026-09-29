<?php

namespace Hubleto\App\Community\HrRecruitment\Models\Migrations;

use Hubleto\Framework\Migration;

class Application_0003 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("UPDATE `hr_applications` a
      JOIN `workflows` w ON w.`id` = (SELECT MIN(w2.`id`) FROM `workflows` w2 WHERE w2.`group` = 'hr_recruitment')
      LEFT JOIN `workflow_steps` s ON s.`id_workflow` = w.`id` AND s.`tag` = CASE
        WHEN LOWER(a.`status`) LIKE '%hired%' THEN 'hr-recruitment-hired'
        WHEN LOWER(a.`status`) LIKE '%reject%' OR LOWER(a.`status`) LIKE '%withdraw%' THEN 'hr-recruitment-rejected'
        WHEN LOWER(a.`stage`) LIKE '%screen%' THEN 'hr-recruitment-screening'
        WHEN LOWER(a.`stage`) LIKE '%interview%' OR LOWER(a.`stage`) LIKE '%assessment%' THEN 'hr-recruitment-interview'
        WHEN LOWER(a.`stage`) LIKE '%offer%' THEN 'hr-recruitment-offer'
        ELSE 'hr-recruitment-applied'
      END
      SET a.`id_workflow` = w.`id`, a.`id_workflow_step` = COALESCE(s.`id`, (SELECT MIN(s2.`id`) FROM `workflow_steps` s2 WHERE s2.`id_workflow` = w.`id`))
      WHERE a.`id_workflow` IS NULL OR a.`id_workflow_step` IS NULL");
    $this->db->execute('ALTER TABLE `hr_applications` DROP INDEX `stage`, DROP INDEX `status`, DROP COLUMN `stage`, DROP COLUMN `status`');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('ALTER TABLE `hr_applications` ADD `stage` varchar(255) NULL DEFAULT NULL, ADD `status` varchar(255) NULL DEFAULT NULL, ADD INDEX `stage` (`stage`), ADD INDEX `status` (`status`)');
    $this->db->execute("UPDATE `hr_applications` a JOIN `workflow_steps` s ON s.`id` = a.`id_workflow_step` SET a.`stage` = s.`name`, a.`status` = CASE WHEN s.`tag` = 'hr-recruitment-hired' THEN 'Hired' WHEN s.`tag` = 'hr-recruitment-rejected' THEN 'Rejected' ELSE 'In progress' END");
  }

  public function upgradeForeignKeys(): void {}

  public function downgradeForeignKeys(): void {}
}