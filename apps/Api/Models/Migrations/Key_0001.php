<?php

namespace Hubleto\App\Community\Api\Models\Migrations;

use Hubleto\Framework\Migration;

class Key_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `api_keys`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `api_keys` (
 `id` int(8) primary key auto_increment,
 `key` varchar(255) ,
 `valid_until` datetime ,
 `is_enabled` int(1) ,
 `notes` text ,
 `ip_address_blacklist` varchar(255) ,
 `ip_address_whitelist` varchar(255) ,
 `id_created_by` int(8) NULL default NULL,
 `created` datetime ,
 index `id` (`id`),
 INDEX `key` (`key`),
 index `valid_until` (`valid_until`),
 index `is_enabled` (`is_enabled`),
 index `id_created_by` (`id_created_by`),
 index `created` (`created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `api_keys`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `api_keys`
          ADD CONSTRAINT `fk_376fc285f4e09b1b417e6d198e944abd`
          FOREIGN KEY (`id_created_by`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `api_keys`
          DROP FOREIGN KEY `fk_376fc285f4e09b1b417e6d198e944abd`;");
  }
}