<?php

namespace Hubleto\App\Community\Api\Models\Migrations;

use Hubleto\Framework\Migration;

class Permission_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `api_permissions`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `api_permissions` (
 `id` int(8) primary key auto_increment,
 `id_key` int(8) NULL default NULL,
 `app` varchar(255) ,
 `controller` varchar(255) ,
 index `id` (`id`),
 index `id_key` (`id_key`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `api_permissions`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `api_permissions`
          ADD CONSTRAINT `fk_895af616127b47b6c9cc5280860fcbc1`
          FOREIGN KEY (`id_key`)
          REFERENCES `api_keys` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `api_permissions`
          DROP FOREIGN KEY `fk_895af616127b47b6c9cc5280860fcbc1`;");
  }
}