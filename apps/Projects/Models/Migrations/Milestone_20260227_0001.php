<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class Milestone_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_milestones`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `projects_milestones` (
 `id` int(8) primary key auto_increment,
 `id_project` int(8) NULL default NULL,
 `title` varchar(255) ,
 `date_due` date ,
 `expected_output` text ,
 `description` text ,
 `color` char(10) ,
 `is_closed` int(1),
 index `id` (`id`),
 index `id_project` (`id_project`),
 index `is_closed` (`is_closed`),
 index `date_due` (`date_due`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_milestones`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_milestones`
          ADD CONSTRAINT `fk_20023db5f5b120e4996f6032d2a52128`
          FOREIGN KEY (`id_project`)
          REFERENCES `projects` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_milestones`
          DROP FOREIGN KEY `fk_20023db5f5b120e4996f6032d2a52128`;");
  }
}