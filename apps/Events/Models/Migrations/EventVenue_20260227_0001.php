<?php

namespace Hubleto\App\Community\Events\Models\Migrations;

use Hubleto\Framework\Migration;

class EventVenue_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_has_venues`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `events_has_venues` (
 `id` int(8) primary key auto_increment,
 `id_event` int(8) NULL default NULL,
 `id_venue` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_event` (`id_event`),
 index `id_venue` (`id_venue`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_has_venues`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `events_has_venues`
          ADD CONSTRAINT `fk_7f05e8b7aaedc7172ab8a198d0e66126`
          FOREIGN KEY (`id_event`)
          REFERENCES `events` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `events_has_venues`
          ADD CONSTRAINT `fk_c3009ef97fd2dda90ec3b7ba181abd9a`
          FOREIGN KEY (`id_venue`)
          REFERENCES `events_venues` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `events_has_venues`
          DROP FOREIGN KEY `fk_7f05e8b7aaedc7172ab8a198d0e66126`; ALTER TABLE `events_has_venues`
          DROP FOREIGN KEY `fk_c3009ef97fd2dda90ec3b7ba181abd9a`;");
  }
}