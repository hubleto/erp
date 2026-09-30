<?php

namespace Hubleto\App\Community\HrRecruitment\Models\Migrations;

use Hubleto\Framework\Migration;

class Application_0002 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute('ALTER TABLE `hr_applications`
      ADD `id_workflow` int(8) NULL DEFAULT NULL,
      ADD `id_workflow_step` int(8) NULL DEFAULT NULL,
      ADD INDEX `id_workflow` (`id_workflow`),
      ADD INDEX `id_workflow_step` (`id_workflow_step`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('ALTER TABLE `hr_applications`
      DROP COLUMN `id_workflow`,
      DROP COLUMN `id_workflow_step`');
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute('ALTER TABLE `hr_applications` ADD CONSTRAINT `fk__hr_applications__id_workflow` FOREIGN KEY (`id_workflow`) REFERENCES `workflows` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
    $this->db->execute('ALTER TABLE `hr_applications` ADD CONSTRAINT `fk__hr_applications__id_workflow_step` FOREIGN KEY (`id_workflow_step`) REFERENCES `workflow_steps` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute('ALTER TABLE `hr_applications` DROP FOREIGN KEY `fk__hr_applications__id_workflow`');
    $this->db->execute('ALTER TABLE `hr_applications` DROP FOREIGN KEY `fk__hr_applications__id_workflow_step`');
  }
}