<?php

namespace Hubleto\App\Community\Dashboards\Models\Migrations;

use Hubleto\Framework\Migration;

class Dashboard_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `dashboards`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `dashboards` (
 `id` int(8) primary key auto_increment,
 `id_owner` int(8) NULL default NULL,
 `title` varchar(255) ,
 `slug` varchar(255) ,
 `color` char(10) ,
 `is_default` int(1) ,
 index `id` (`id`),
 index `id_owner` (`id_owner`),
 index `is_default` (`is_default`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `dashboards`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `dashboards`
          ADD CONSTRAINT `fk_6a123364b2d42602b9c1b74c6db08d90`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `dashboards`
          DROP FOREIGN KEY `fk_6a123364b2d42602b9c1b74c6db08d90`;");
  }
}