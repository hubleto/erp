<?php

namespace Hubleto\App\Community\Contacts\Models\Migrations;

use Hubleto\Framework\Migration;

class Contact_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `contacts`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `contacts` (
 `salutation` varchar(255) ,
 `title_before` varchar(255) ,
 `first_name` varchar(255) ,
 `middle_name` varchar(255) ,
 `last_name` varchar(255) ,
 `title_after` varchar(255) ,
 `id_customer` int(8) NULL default NULL,
 `is_primary` int(1) ,
 `is_for_invoicing` int(1) ,
 `note` text ,
 `date_created` date ,
 `is_valid` int(1) ,
 `id` int(8) primary key auto_increment,
 index `id_customer` (`id_customer`),
 index `is_primary` (`is_primary`),
 index `is_for_invoicing` (`is_for_invoicing`),
 index `date_created` (`date_created`),
 index `is_valid` (`is_valid`),
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `contacts`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `contacts`
          ADD CONSTRAINT `fk_39be0526a1a4eb60f8bcdc0872bf0825`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `contacts`
          DROP FOREIGN KEY `fk_39be0526a1a4eb60f8bcdc0872bf0825`;");
  }
}