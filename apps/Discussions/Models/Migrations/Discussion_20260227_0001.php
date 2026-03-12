<?php

namespace Hubleto\App\Community\Discussions\Models\Migrations;

use Hubleto\Framework\Migration;

class Discussion_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `discussions`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `discussions` (
 `id` int(8) primary key auto_increment,
 `topic` varchar(255) ,
 `description` text ,
 `id_main_mod` int(8) NULL default NULL,
 `is_closed` int(1) ,
 `notes` text ,
 `date_created` datetime ,
 index `id` (`id`),
 index `id_main_mod` (`id_main_mod`),
 index `is_closed` (`is_closed`),
 index `date_created` (`date_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `discussions`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `discussions`
          ADD CONSTRAINT `fk_95ae9e0ae84d0d04504b7f454dfa79de`
          FOREIGN KEY (`id_main_mod`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `discussions`
          DROP FOREIGN KEY `fk_95ae9e0ae84d0d04504b7f454dfa79de`;");
  }
}