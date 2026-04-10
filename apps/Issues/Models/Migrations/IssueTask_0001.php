<?php

namespace Hubleto\App\Community\Issues\Models\Migrations;

use Hubleto\Framework\Migration;

class IssueTask_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `issues_tasks`;
      create table `issues_tasks` (
        `id` int(8) primary key auto_increment,
        `id_issue` int(8) NULL default NULL,
        `id_task` int(8) NULL default NULL,
        index `id` (`id`),
        index `id_issue` (`id_issue`),
        index `id_task` (`id_task`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    
      ALTER TABLE `issues_tasks` add unique index `id_task_unique` (`id_task`);
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `issues_tasks`;
      set foreign_key_checks = 1;

      ALTER TABLE `issues_tasks` drop index (`id_task_unique`);
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `issues_tasks`
      ADD CONSTRAINT `fk__issues_tasks__id_issue`
      FOREIGN KEY (`id_issue`)
      REFERENCES `issues` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `issues_tasks`
      ADD CONSTRAINT `fk__issues_tasks__id_task`
      FOREIGN KEY (`id_task`)
      REFERENCES `tasks` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `issues_tasks` DROP FOREIGN KEY `fk__issues_tasks__id_issue`;
      ALTER TABLE `issues_tasks` DROP FOREIGN KEY `fk__issues_tasks__id_task`;
    ");
  }
}