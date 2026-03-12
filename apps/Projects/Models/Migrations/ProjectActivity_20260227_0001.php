<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class ProjectActivity_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `project_activities`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `project_activities` (
 `id` int(8) primary key auto_increment,
 `subject` varchar(255) ,
 `location` varchar(255) ,
 `online_meeting_link` varchar(255) ,
 `description` text ,
 `id_activity_type` int(8) NULL default NULL,
 `date_start` date ,
 `time_start` time ,
 `date_end` date ,
 `time_end` time ,
 `recurrence` text ,
 `all_day` int(1) ,
 `completed` int(1) ,
 `meeting_minutes_link` varchar(255) ,
 `meeting_minutes` text ,
 `id_owner` int(8) NULL default NULL,
 `id_project` int(8) NULL default NULL,
 `id_contact` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_activity_type` (`id_activity_type`),
 index `date_start` (`date_start`),
 index `time_start` (`time_start`),
 index `date_end` (`date_end`),
 index `time_end` (`time_end`),
 index `all_day` (`all_day`),
 index `completed` (`completed`),
 index `id_owner` (`id_owner`),
 index `id_project` (`id_project`),
 index `id_contact` (`id_contact`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `project_activities`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `project_activities`
          ADD CONSTRAINT `fk_4cf2e48a0ea7fe501e21545d042efd1e`
          FOREIGN KEY (`id_activity_type`)
          REFERENCES `activity_types` (`id`)
          ON DELETE SET NULL
          ON UPDATE SET NULL; ALTER TABLE `project_activities`
          ADD CONSTRAINT `fk_4b9e9cec1fecb1fedf576f92df4c814c`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `project_activities`
          ADD CONSTRAINT `fk_2884e00d56d37e708fa1dd8468bec302`
          FOREIGN KEY (`id_project`)
          REFERENCES `projects` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `project_activities`
          ADD CONSTRAINT `fk_fb4b4c58fe7a23dcdbfcfb3537815bd1`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `project_activities`
          DROP FOREIGN KEY `fk_4cf2e48a0ea7fe501e21545d042efd1e`; ALTER TABLE `project_activities`
          DROP FOREIGN KEY `fk_4b9e9cec1fecb1fedf576f92df4c814c`; ALTER TABLE `project_activities`
          DROP FOREIGN KEY `fk_2884e00d56d37e708fa1dd8468bec302`; ALTER TABLE `project_activities`
          DROP FOREIGN KEY `fk_fb4b4c58fe7a23dcdbfcfb3537815bd1`;");
  }
}