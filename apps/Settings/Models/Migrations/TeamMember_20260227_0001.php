<?php

namespace Hubleto\App\Community\Settings\Models\Migrations;

use Hubleto\Framework\Migration;

class TeamMember_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `teams_members`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `teams_members` (
 `id` int(8) primary key auto_increment,
 `id_team` int(8) NULL default NULL,
 `id_member` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_team` (`id_team`),
 index `id_member` (`id_member`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `teams_members`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `teams_members`
          ADD CONSTRAINT `fk_d7abf9f6c83b8dc1afef7b8b8c200a36`
          FOREIGN KEY (`id_team`)
          REFERENCES `teams` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `teams_members`
          ADD CONSTRAINT `fk_4c55a7cb6447ff011558dc09a7eb694d`
          FOREIGN KEY (`id_member`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `teams_members`
          DROP FOREIGN KEY `fk_d7abf9f6c83b8dc1afef7b8b8c200a36`; ALTER TABLE `teams_members`
          DROP FOREIGN KEY `fk_4c55a7cb6447ff011558dc09a7eb694d`;");
  }
}