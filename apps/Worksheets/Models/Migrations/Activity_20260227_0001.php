<?php

namespace Hubleto\App\Community\Worksheets\Models\Migrations;

use Hubleto\Framework\Migration;

class Activity_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `worksheet_activities`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `worksheet_activities` (
 `id` int(8) primary key auto_increment,
 `id_worker` int(8) NULL default NULL,
 `id_task` int(8) NULL default NULL,
 `id_type` int(8) NULL default NULL,
 `date_worked` date ,
 `worked_hours` decimal(14, 2) ,
 `description` text ,
 `is_approved` int(1) ,
 `is_chargeable` int(1) ,
 `datetime_created` datetime ,
 index `id` (`id`),
 index `id_worker` (`id_worker`),
 index `id_task` (`id_task`),
 index `id_type` (`id_type`),
 index `date_worked` (`date_worked`),
 index `is_approved` (`is_approved`),
 index `is_chargeable` (`is_chargeable`),
 index `datetime_created` (`datetime_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `worksheet_activities`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `worksheet_activities`
          ADD CONSTRAINT `fk_02c53bc7533cf0474ff7391b13dcc05c`
          FOREIGN KEY (`id_worker`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `worksheet_activities`
          ADD CONSTRAINT `fk_477673e502b2001b593c797a659447d6`
          FOREIGN KEY (`id_task`)
          REFERENCES `tasks` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `worksheet_activities`
          ADD CONSTRAINT `fk_3e867f5d063e459ef4993074137e95b3`
          FOREIGN KEY (`id_type`)
          REFERENCES `worksheet_activities_types` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `worksheet_activities`
          DROP FOREIGN KEY `fk_02c53bc7533cf0474ff7391b13dcc05c`; ALTER TABLE `worksheet_activities`
          DROP FOREIGN KEY `fk_477673e502b2001b593c797a659447d6`; ALTER TABLE `worksheet_activities`
          DROP FOREIGN KEY `fk_3e867f5d063e459ef4993074137e95b3`;");
  }
}