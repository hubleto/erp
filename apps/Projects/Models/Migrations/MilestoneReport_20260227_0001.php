<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class MilestoneReport_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_milestone_reports`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `projects_milestone_reports` (
 `id` int(8) primary key auto_increment,
 `id_milestone` int(8) NULL default NULL,
 `summary` varchar(255) ,
 `details` text ,
 `progress_percent` int(255) ,
 `date_report` date ,
 `id_reported_by` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_milestone` (`id_milestone`),
 index `progress_percent` (`progress_percent`),
 index `date_report` (`date_report`),
 index `id_reported_by` (`id_reported_by`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_milestone_reports`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_milestone_reports`
          ADD CONSTRAINT `fk_4961876e4967f64fd9ad899911e2389e`
          FOREIGN KEY (`id_milestone`)
          REFERENCES `projects_milestones` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects_milestone_reports`
          ADD CONSTRAINT `fk_3aebeffdde90ae8aa3149a2c0744ba20`
          FOREIGN KEY (`id_reported_by`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_milestone_reports`
          DROP FOREIGN KEY `fk_4961876e4967f64fd9ad899911e2389e`; ALTER TABLE `projects_milestone_reports`
          DROP FOREIGN KEY `fk_3aebeffdde90ae8aa3149a2c0744ba20`;");
  }
}