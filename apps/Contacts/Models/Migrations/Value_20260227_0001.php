<?php

namespace Hubleto\App\Community\Contacts\Models\Migrations;

use Hubleto\Framework\Migration;

class Value_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `contact_values`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `contact_values` (
 `id` int(8) primary key auto_increment,
 `id_contact` int(8) NULL default NULL,
 `id_category` int(8) NULL default NULL,
 `type` varchar(255) ,
 `value` varchar(255) ,
 index `id` (`id`),
 index `id_contact` (`id_contact`),
 index `id_category` (`id_category`),
 INDEX `type` (`type`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `contact_values`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `contact_values`
          ADD CONSTRAINT `fk_bd302534361eb90daeaf847a8297446a`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE CASCADE
          ON UPDATE CASCADE; ALTER TABLE `contact_values`
          ADD CONSTRAINT `fk_c6f06e714d235f4827af8e36960a3f45`
          FOREIGN KEY (`id_category`)
          REFERENCES `contact_categories` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `contact_values`
          DROP FOREIGN KEY `fk_bd302534361eb90daeaf847a8297446a`; ALTER TABLE `contact_values`
          DROP FOREIGN KEY `fk_c6f06e714d235f4827af8e36960a3f45`;");
  }
}