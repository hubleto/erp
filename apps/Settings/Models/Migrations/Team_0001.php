<?php

namespace Hubleto\App\Community\Settings\Models\Migrations;

use Hubleto\Framework\Migration;

class Team_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `teams`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `teams` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `color` char(10) ,
 `description` text ,
 `id_manager` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_manager` (`id_manager`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `teams`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `teams`
          ADD CONSTRAINT `fk_1b99f335972ad0f0526989f986c6bb0d`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `teams`
          DROP FOREIGN KEY `fk_1b99f335972ad0f0526989f986c6bb0d`;");
  }
}