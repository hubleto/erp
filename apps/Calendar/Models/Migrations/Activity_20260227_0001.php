<?php

namespace Hubleto\App\Community\Calendar\Models\Migrations;

use Hubleto\Framework\Migration;

class Activity_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `activities`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `activities` (
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
 index `id` (`id`),
 index `id_activity_type` (`id_activity_type`),
 index `date_start` (`date_start`),
 index `time_start` (`time_start`),
 index `date_end` (`date_end`),
 index `time_end` (`time_end`),
 index `all_day` (`all_day`),
 index `completed` (`completed`),
 index `id_owner` (`id_owner`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `activities`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `activities`
          ADD CONSTRAINT `fk_3ccebdaf6bf464fb9dfffb10aca32f9a`
          FOREIGN KEY (`id_activity_type`)
          REFERENCES `activity_types` (`id`)
          ON DELETE SET NULL
          ON UPDATE SET NULL; ALTER TABLE `activities`
          ADD CONSTRAINT `fk_64e8e6e41ab81ec2a38b0270010d7cab`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `activities`
          DROP FOREIGN KEY `fk_3ccebdaf6bf464fb9dfffb10aca32f9a`; ALTER TABLE `activities`
          DROP FOREIGN KEY `fk_64e8e6e41ab81ec2a38b0270010d7cab`;");
  }
}