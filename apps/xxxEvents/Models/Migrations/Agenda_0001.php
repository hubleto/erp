<?php

namespace Hubleto\App\Community\Events\Models\Migrations;

use Hubleto\Framework\Migration;

class Agenda_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_agendas`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `events_agendas` (
 `id` int(8) primary key auto_increment,
 `id_event` int(8) NULL default NULL,
 `title` varchar(255) ,
 `topic` varchar(255) ,
 `description` text ,
 `floor` varchar(255) ,
 `room` varchar(255) ,
 `datetime_start` datetime ,
 `datetime_end` datetime ,
 index `id` (`id`),
 index `id_event` (`id_event`),
 index `datetime_start` (`datetime_start`),
 index `datetime_end` (`datetime_end`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_agendas`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `events_agendas`
          ADD CONSTRAINT `fk_fe24d2d53bff5dbe3fdb52cd7dc7cd0c`
          FOREIGN KEY (`id_event`)
          REFERENCES `events` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `events_agendas`
          DROP FOREIGN KEY `fk_fe24d2d53bff5dbe3fdb52cd7dc7cd0c`;");
  }
}