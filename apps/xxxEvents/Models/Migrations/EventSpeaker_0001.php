<?php

namespace Hubleto\App\Community\Events\Models\Migrations;

use Hubleto\Framework\Migration;

class EventSpeaker_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_has_speakers`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `events_has_speakers` (
 `id` int(8) primary key auto_increment,
 `id_event` int(8) NULL default NULL,
 `id_speaker` int(8) NULL default NULL,
 `is_attending_virtually` int(1) ,
 index `id` (`id`),
 index `id_event` (`id_event`),
 index `id_speaker` (`id_speaker`),
 index `is_attending_virtually` (`is_attending_virtually`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_has_speakers`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `events_has_speakers`
          ADD CONSTRAINT `fk_e84360e92474bc7d6c6a09fddf943de8`
          FOREIGN KEY (`id_event`)
          REFERENCES `events` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `events_has_speakers`
          ADD CONSTRAINT `fk_ef8cfedfa1273f3feade0ea1e678afab`
          FOREIGN KEY (`id_speaker`)
          REFERENCES `events_speakers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `events_has_speakers`
          DROP FOREIGN KEY `fk_e84360e92474bc7d6c6a09fddf943de8`; ALTER TABLE `events_has_speakers`
          DROP FOREIGN KEY `fk_ef8cfedfa1273f3feade0ea1e678afab`;");
  }
}