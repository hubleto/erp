<?php

namespace Hubleto\App\Community\EventRegistrations\Models\Migrations;

use Hubleto\Framework\Migration;

class Contact_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `my_app_contacts`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `my_app_contacts` (
 `id` int(8) primary key auto_increment,
 `first_name` varchar(255) ,
 `last_name` varchar(255) ,
 `id_manager` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_manager` (`id_manager`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `my_app_contacts`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `my_app_contacts`
          ADD CONSTRAINT `fk_2a3b93f7447e8acf6dce3dc8997d5bd2`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `my_app_contacts`
          DROP FOREIGN KEY `fk_2a3b93f7447e8acf6dce3dc8997d5bd2`;");
  }
}