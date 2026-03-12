<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class ProjectTask_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_tasks`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `projects_tasks` (
 `id` int(8) primary key auto_increment,
 `id_project` int(8) NULL default NULL,
 `id_task` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_project` (`id_project`),
 index `id_task` (`id_task`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_tasks`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_tasks`
          ADD CONSTRAINT `fk_3ad199ed58350d39c0bbc8859cc47aab`
          FOREIGN KEY (`id_project`)
          REFERENCES `projects` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects_tasks`
          ADD CONSTRAINT `fk_f36c1993ff8af45389ab62c01042e82f`
          FOREIGN KEY (`id_task`)
          REFERENCES `tasks` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_tasks`
          DROP FOREIGN KEY `fk_3ad199ed58350d39c0bbc8859cc47aab`; ALTER TABLE `projects_tasks`
          DROP FOREIGN KEY `fk_f36c1993ff8af45389ab62c01042e82f`;");
  }
}