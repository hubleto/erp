<?php

namespace Hubleto\App\Community\Notifications\Models\Migrations;

use Hubleto\Framework\Migration;

class Notification_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `notifications`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `notifications` (
 `id` int(8) primary key auto_increment,
 `priority` int(255) ,
 `id_from` int(8) NULL default NULL,
 `id_to` int(8) NULL default NULL,
 `subject` varchar(255) ,
 `body` text ,
 `url` varchar(255) ,
 `category` int(255) ,
 `color` char(10) ,
 `tags` text ,
 `datetime_sent` datetime ,
 `datetime_read` datetime ,
 index `id` (`id`),
 index `priority` (`priority`),
 index `id_from` (`id_from`),
 index `id_to` (`id_to`),
 index `category` (`category`),
 index `datetime_sent` (`datetime_sent`),
 index `datetime_read` (`datetime_read`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `notifications`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `notifications`
          ADD CONSTRAINT `fk_38408abb4c1d9d70fb982acdfd27608b`
          FOREIGN KEY (`id_from`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `notifications`
          ADD CONSTRAINT `fk_d508017d3eaff68a714fa1f5581594fd`
          FOREIGN KEY (`id_to`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `notifications`
          DROP FOREIGN KEY `fk_38408abb4c1d9d70fb982acdfd27608b`; ALTER TABLE `notifications`
          DROP FOREIGN KEY `fk_d508017d3eaff68a714fa1f5581594fd`;");
  }
}