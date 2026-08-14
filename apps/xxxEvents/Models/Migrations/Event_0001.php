<?php

namespace Hubleto\App\Community\Events\Models\Migrations;

use Hubleto\Framework\Migration;

class Event_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `events` (
 `id` int(8) primary key auto_increment,
 `title` varchar(255) ,
 `id_type` int(8) NULL default NULL,
 `attendance_options` int(255) ,
 `brief_description` text ,
 `full_description` text ,
 `date_start` date ,
 `date_end` date ,
 `id_organizer` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_type` (`id_type`),
 index `attendance_options` (`attendance_options`),
 index `date_start` (`date_start`),
 index `date_end` (`date_end`),
 index `id_organizer` (`id_organizer`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `events`
          ADD CONSTRAINT `fk_55cf82503bc564dfa55fb11a758f615f`
          FOREIGN KEY (`id_type`)
          REFERENCES `events_types` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `events`
          ADD CONSTRAINT `fk_d78463e3547706bc488865cdf1c147d2`
          FOREIGN KEY (`id_organizer`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `events`
          DROP FOREIGN KEY `fk_55cf82503bc564dfa55fb11a758f615f`; ALTER TABLE `events`
          DROP FOREIGN KEY `fk_d78463e3547706bc488865cdf1c147d2`;");
  }
}