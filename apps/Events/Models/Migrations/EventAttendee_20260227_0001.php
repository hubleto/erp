<?php

namespace Hubleto\App\Community\Events\Models\Migrations;

use Hubleto\Framework\Migration;

class EventAttendee_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_has_attendees`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `events_has_attendees` (
 `id` int(8) primary key auto_increment,
 `id_event` int(8) NULL default NULL,
 `id_attendee` int(8) NULL default NULL,
 `is_attending_virtually` int(1) ,
 index `id` (`id`),
 index `id_event` (`id_event`),
 index `id_attendee` (`id_attendee`),
 index `is_attending_virtually` (`is_attending_virtually`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_has_attendees`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `events_has_attendees`
          ADD CONSTRAINT `fk_b81ef7536cd7b80fbc140fb236636a2b`
          FOREIGN KEY (`id_event`)
          REFERENCES `events` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `events_has_attendees`
          ADD CONSTRAINT `fk_7722c2ebc1108e8f82b5337fd4f3d3f3`
          FOREIGN KEY (`id_attendee`)
          REFERENCES `events_attendees` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `events_has_attendees`
          DROP FOREIGN KEY `fk_b81ef7536cd7b80fbc140fb236636a2b`; ALTER TABLE `events_has_attendees`
          DROP FOREIGN KEY `fk_7722c2ebc1108e8f82b5337fd4f3d3f3`;");
  }
}