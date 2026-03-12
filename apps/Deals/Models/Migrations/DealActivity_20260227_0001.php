<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class DealActivity_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deal_activities`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `deal_activities` (
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
 `id_deal` int(8) NULL default NULL,
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
 index `id_deal` (`id_deal`),
 index `id_contact` (`id_contact`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deal_activities`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deal_activities`
          ADD CONSTRAINT `fk_1f9e69f06c33cdf9e9a4ce9610f17aa1`
          FOREIGN KEY (`id_activity_type`)
          REFERENCES `activity_types` (`id`)
          ON DELETE SET NULL
          ON UPDATE SET NULL; ALTER TABLE `deal_activities`
          ADD CONSTRAINT `fk_b596b33899483b9709c498da04149487`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deal_activities`
          ADD CONSTRAINT `fk_c5578db5e871407997f2c718a44ae825`
          FOREIGN KEY (`id_deal`)
          REFERENCES `deals` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deal_activities`
          ADD CONSTRAINT `fk_a6e62c9066974341b92accf63fff08ef`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deal_activities`
          DROP FOREIGN KEY `fk_1f9e69f06c33cdf9e9a4ce9610f17aa1`; ALTER TABLE `deal_activities`
          DROP FOREIGN KEY `fk_b596b33899483b9709c498da04149487`; ALTER TABLE `deal_activities`
          DROP FOREIGN KEY `fk_c5578db5e871407997f2c718a44ae825`; ALTER TABLE `deal_activities`
          DROP FOREIGN KEY `fk_a6e62c9066974341b92accf63fff08ef`;");
  }
}