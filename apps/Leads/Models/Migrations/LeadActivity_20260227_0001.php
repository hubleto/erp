<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

class LeadActivity_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `lead_activities`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `lead_activities` (
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
 `id_lead` int(8) NULL default NULL,
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
 index `id_lead` (`id_lead`),
 index `id_contact` (`id_contact`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `lead_activities`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `lead_activities`
          ADD CONSTRAINT `fk_b513709d5eb7a3a6e30008caaa0c26ca`
          FOREIGN KEY (`id_activity_type`)
          REFERENCES `activity_types` (`id`)
          ON DELETE SET NULL
          ON UPDATE SET NULL; ALTER TABLE `lead_activities`
          ADD CONSTRAINT `fk_7e4fc8c4b4e47bd291e3630a5eb24d40`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `lead_activities`
          ADD CONSTRAINT `fk_e3660bef19d9e9fcfd1b04004024b7b3`
          FOREIGN KEY (`id_lead`)
          REFERENCES `leads` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `lead_activities`
          ADD CONSTRAINT `fk_3d0ca774a74ab2524c788b7055e94205`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `lead_activities`
          DROP FOREIGN KEY `fk_b513709d5eb7a3a6e30008caaa0c26ca`; ALTER TABLE `lead_activities`
          DROP FOREIGN KEY `fk_7e4fc8c4b4e47bd291e3630a5eb24d40`; ALTER TABLE `lead_activities`
          DROP FOREIGN KEY `fk_e3660bef19d9e9fcfd1b04004024b7b3`; ALTER TABLE `lead_activities`
          DROP FOREIGN KEY `fk_3d0ca774a74ab2524c788b7055e94205`;");
  }
}