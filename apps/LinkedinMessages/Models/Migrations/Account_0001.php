<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models\Migrations;

use Hubleto\Framework\Migration;

class Account_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `linkedin_accounts`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `linkedin_accounts` (
 `id` int(8) primary key auto_increment,
 `id_owner` int(8) NULL default NULL,
 `linkedin_member_id` varchar(255) ,
 `name` varchar(255) ,
 `headline` varchar(255) ,
 `email` varchar(255) ,
 `profile_url` varchar(255) ,
 `picture_url` varchar(255) ,
 `locale` varchar(255) ,
 `access_token` text ,
 `refresh_token` text ,
 `token_expires_at` datetime ,
 `scopes` varchar(255) ,
 `status` int(255) ,
 `last_sync_at` datetime ,
 `last_sync_info` text ,
 `changelog_cursor` varchar(255) ,
 `date_created` datetime ,
 index `id` (`id`),
 index `id_owner` (`id_owner`),
 index `token_expires_at` (`token_expires_at`),
 index `status` (`status`),
 index `last_sync_at` (`last_sync_at`),
 index `date_created` (`date_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `linkedin_accounts`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `linkedin_accounts`
          ADD CONSTRAINT `fk_b624a7f32f994909a2b3fc9f477b25d3`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `linkedin_accounts`
          DROP FOREIGN KEY `fk_b624a7f32f994909a2b3fc9f477b25d3`;");
  }
}
