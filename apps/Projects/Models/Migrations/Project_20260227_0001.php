<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class Project_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `projects` (
 `id` int(8) primary key auto_increment,
 `id_deal` int(8) NULL default NULL,
 `id_customer` int(8) NULL default NULL,
 `id_contact` int(8) NULL default NULL,
 `identifier` varchar(255) ,
 `title` varchar(255) ,
 `description` text ,
 `id_main_developer` int(8) NULL default NULL,
 `id_project_manager` int(8) NULL default NULL,
 `id_account_manager` int(8) NULL default NULL,
 `shared_with` text ,
 `priority` int(255) ,
 `date_start` date ,
 `date_deadline` date ,
 `average_hourly_costs` decimal(14, 4) ,
 `budget` decimal(14, 4) ,
 `id_workflow` int(8) NULL default NULL,
 `id_workflow_step` int(8) NULL default NULL,
 `is_closed` int(1) ,
 `color` char(10) ,
 `online_documentation_folder` varchar(255) ,
 `notes` text ,
 `date_created` datetime ,
 index `id` (`id`),
 index `id_deal` (`id_deal`),
 index `id_customer` (`id_customer`),
 index `id_contact` (`id_contact`),
 index `id_main_developer` (`id_main_developer`),
 index `id_project_manager` (`id_project_manager`),
 index `id_account_manager` (`id_account_manager`),
 index `priority` (`priority`),
 index `date_start` (`date_start`),
 index `date_deadline` (`date_deadline`),
 index `id_workflow` (`id_workflow`),
 index `id_workflow_step` (`id_workflow_step`),
 index `is_closed` (`is_closed`),
 index `date_created` (`date_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects`
          ADD CONSTRAINT `fk_99f5bd1e2c20a16652f28ca2391b7a48`
          FOREIGN KEY (`id_deal`)
          REFERENCES `deals` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects`
          ADD CONSTRAINT `fk_3b9d404e1b1d7811ae2508016c524769`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects`
          ADD CONSTRAINT `fk_d0fdf11c13892fd88720cc3db6651b48`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects`
          ADD CONSTRAINT `fk_113b52325626f6abcf3ba2da044e32a3`
          FOREIGN KEY (`id_main_developer`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects`
          ADD CONSTRAINT `fk_f6caf692bd59536dbb2cf87398c509da`
          FOREIGN KEY (`id_project_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects`
          ADD CONSTRAINT `fk_d6c2be480de682068c3111c0ed0e1f7c`
          FOREIGN KEY (`id_account_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects`
          ADD CONSTRAINT `fk_dbd65a474bbc7c3453c0847efc4f4f6e`
          FOREIGN KEY (`id_workflow`)
          REFERENCES `workflows` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects`
          ADD CONSTRAINT `fk_daab30255f5ae243aee7fb67b511988b`
          FOREIGN KEY (`id_workflow_step`)
          REFERENCES `workflow_steps` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects`
          DROP FOREIGN KEY `fk_99f5bd1e2c20a16652f28ca2391b7a48`; ALTER TABLE `projects`
          DROP FOREIGN KEY `fk_3b9d404e1b1d7811ae2508016c524769`; ALTER TABLE `projects`
          DROP FOREIGN KEY `fk_d0fdf11c13892fd88720cc3db6651b48`; ALTER TABLE `projects`
          DROP FOREIGN KEY `fk_113b52325626f6abcf3ba2da044e32a3`; ALTER TABLE `projects`
          DROP FOREIGN KEY `fk_f6caf692bd59536dbb2cf87398c509da`; ALTER TABLE `projects`
          DROP FOREIGN KEY `fk_d6c2be480de682068c3111c0ed0e1f7c`; ALTER TABLE `projects`
          DROP FOREIGN KEY `fk_dbd65a474bbc7c3453c0847efc4f4f6e`; ALTER TABLE `projects`
          DROP FOREIGN KEY `fk_daab30255f5ae243aee7fb67b511988b`;");
  }
}