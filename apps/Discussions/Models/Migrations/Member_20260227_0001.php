<?php

namespace Hubleto\App\Community\Discussions\Models\Migrations;

use Hubleto\Framework\Migration;

class Member_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `discussions_members`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `discussions_members` (
 `id` int(8) primary key auto_increment,
 `id_discussion` int(8) NULL default NULL,
 `id_member` int(8) NULL default NULL,
 `permissions` text ,
 index `id` (`id`),
 index `id_discussion` (`id_discussion`),
 index `id_member` (`id_member`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `discussions_members`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `discussions_members`
          ADD CONSTRAINT `fk_c86c30d8d938fabe0683e817213f0236`
          FOREIGN KEY (`id_discussion`)
          REFERENCES `discussions` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `discussions_members`
          ADD CONSTRAINT `fk_1aaab47394ea772d148172661581a18b`
          FOREIGN KEY (`id_member`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `discussions_members`
          DROP FOREIGN KEY `fk_c86c30d8d938fabe0683e817213f0236`; ALTER TABLE `discussions_members`
          DROP FOREIGN KEY `fk_1aaab47394ea772d148172661581a18b`;");
  }
}