<?php

namespace Hubleto\App\Community\Settings\Models\Migrations;

use Hubleto\Framework\Migration;

class Setting_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `settings`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `settings` (
 `id` int(8) primary key auto_increment,
 `key` varchar(255) ,
 `value` text ,
 `id_owner` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_owner` (`id_owner`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;


              alter table `settings`
              add constraint `key` unique (`key` asc)
            ;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `settings`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `settings`
          ADD CONSTRAINT `fk_73d809b10c892beadd6f39d0849c2cb9`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `settings`
          DROP FOREIGN KEY `fk_73d809b10c892beadd6f39d0849c2cb9`;");
  }
}