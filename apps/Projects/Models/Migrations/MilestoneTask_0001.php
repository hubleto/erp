<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class MilestoneTask_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `projects_milestones_tasks`;
      create table `projects_milestones_tasks` (
        `id` int(8) primary key auto_increment,
        `id_milestone` int(8) NULL default NULL,
        `id_task` int(8) NULL default NULL,
        index `id` (`id`),
        index `id_milestone` (`id_milestone`),
        index `id_task` (`id_task`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `projects_milestones_tasks`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `projects_milestones_tasks` ADD CONSTRAINT `fk__projects_milestones_tasks__id_milestone`
      FOREIGN KEY (`id_milestone`) REFERENCES `projects_milestones` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `projects_milestones_tasks` ADD CONSTRAINT `fk__projects_milestones_tasks__id_task`
      FOREIGN KEY (`id_task`) REFERENCES `tasks` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `projects_milestones_tasks`
      DROP FOREIGN KEY `fk__projects_milestones_tasks__id_milestone`;
      ALTER TABLE `projects_milestones_tasks`
      DROP FOREIGN KEY `fk__projects_milestones_tasks__id_task`;
    ");
  }
}