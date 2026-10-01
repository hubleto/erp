<?php

namespace Hubleto\App\Community\LinkedinMessages\Models\Migrations;

use Hubleto\Framework\Migration;

class Message_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `linkedin_messages`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `linkedin_messages` (
 `id` int(8) primary key auto_increment,
 `id_account` int(8) NULL default NULL,
 `id_profile` int(8) NULL default NULL,
 `id_campaign` int(8) NULL default NULL,
 `id_owner` int(8) NULL default NULL,
 `linkedin_message_id` varchar(255) ,
 `thread_id` varchar(255) ,
 `direction` int(255) ,
 `subject` varchar(255) ,
 `body` text ,
 `sent_at` datetime ,
 `is_read` int(1) ,
 `note` text ,
 `date_created` datetime ,
 index `id` (`id`),
 index `id_account` (`id_account`),
 index `id_profile` (`id_profile`),
 index `id_campaign` (`id_campaign`),
 index `id_owner` (`id_owner`),
 index `direction` (`direction`),
 index `sent_at` (`sent_at`),
 index `is_read` (`is_read`),
 index `date_created` (`date_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `linkedin_messages`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `linkedin_messages`
          ADD CONSTRAINT `fk_725329a7eb03ab0c78ec8a7b44f4d489`
          FOREIGN KEY (`id_account`)
          REFERENCES `linkedin_accounts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `linkedin_messages`
          ADD CONSTRAINT `fk_59c7455edd2574396457f105c0fab164`
          FOREIGN KEY (`id_profile`)
          REFERENCES `linkedin_profiles` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `linkedin_messages`
          ADD CONSTRAINT `fk_75a0d7c98c3129f1312d3d89c90f23d7`
          FOREIGN KEY (`id_campaign`)
          REFERENCES `linkedin_campaigns` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `linkedin_messages`
          ADD CONSTRAINT `fk_91dde8318113c77ed2bc337a5fea71ed`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `linkedin_messages`
          DROP FOREIGN KEY `fk_725329a7eb03ab0c78ec8a7b44f4d489`; ALTER TABLE `linkedin_messages`
          DROP FOREIGN KEY `fk_59c7455edd2574396457f105c0fab164`; ALTER TABLE `linkedin_messages`
          DROP FOREIGN KEY `fk_75a0d7c98c3129f1312d3d89c90f23d7`; ALTER TABLE `linkedin_messages`
          DROP FOREIGN KEY `fk_91dde8318113c77ed2bc337a5fea71ed`;");
  }
}
