<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models\Migrations;

use Hubleto\Framework\Migration;

class Reply_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `linkedin_replies`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `linkedin_replies` (
 `id` int(8) primary key auto_increment,
 `id_message` int(8) NULL default NULL,
 `id_owner` int(8) NULL default NULL,
 `body` text ,
 `status` int(255) ,
 `error_info` text ,
 `sent_at` datetime ,
 `date_created` datetime ,
 index `id` (`id`),
 index `id_message` (`id_message`),
 index `id_owner` (`id_owner`),
 index `status` (`status`),
 index `sent_at` (`sent_at`),
 index `date_created` (`date_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `linkedin_replies`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `linkedin_replies`
          ADD CONSTRAINT `fk_9deda93b1a8eb092f5f83f057a1805e0`
          FOREIGN KEY (`id_message`)
          REFERENCES `linkedin_messages` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `linkedin_replies`
          ADD CONSTRAINT `fk_4ce4a46f84294b92604f46abdc33db72`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `linkedin_replies`
          DROP FOREIGN KEY `fk_9deda93b1a8eb092f5f83f057a1805e0`; ALTER TABLE `linkedin_replies`
          DROP FOREIGN KEY `fk_4ce4a46f84294b92604f46abdc33db72`;");
  }
}
