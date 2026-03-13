<?php

namespace Hubleto\App\Community\Calendar\Models\Migrations;

use Hubleto\Framework\Migration;

class SharedCalendar_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `shared_calendars`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `shared_calendars` (
 `id` int(8) primary key auto_increment,
 `id_owner` int(8) NULL default NULL,
 `calendar` varchar(255) ,
 `share_key` varchar(255) ,
 `view_details` int(1) ,
 `enabled` int(1) ,
 `date_from` date ,
 `date_to` date ,
 index `id` (`id`),
 index `id_owner` (`id_owner`),
 index `view_details` (`view_details`),
 index `enabled` (`enabled`),
 index `date_from` (`date_from`),
 index `date_to` (`date_to`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `shared_calendars`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `shared_calendars`
          ADD CONSTRAINT `fk_fd2e485dd5322272909d89ada0157eff`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `shared_calendars`
          DROP FOREIGN KEY `fk_fd2e485dd5322272909d89ada0157eff`;");
  }
}