<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models\Migrations;

use Hubleto\Framework\Migration;

class MessageTag_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cross_linkedin_message_tags`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cross_linkedin_message_tags` (
 `id` int(8) primary key auto_increment,
 `id_message` int(8) NULL default NULL,
 `id_tag` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_message` (`id_message`),
 index `id_tag` (`id_tag`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cross_linkedin_message_tags`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cross_linkedin_message_tags`
          ADD CONSTRAINT `fk_ef50e0f10bc8d51c8c4c8ece3274dcfa`
          FOREIGN KEY (`id_message`)
          REFERENCES `linkedin_messages` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `cross_linkedin_message_tags`
          ADD CONSTRAINT `fk_b13606644ece846ea2315145c4d389c2`
          FOREIGN KEY (`id_tag`)
          REFERENCES `linkedin_tags` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cross_linkedin_message_tags`
          DROP FOREIGN KEY `fk_ef50e0f10bc8d51c8c4c8ece3274dcfa`; ALTER TABLE `cross_linkedin_message_tags`
          DROP FOREIGN KEY `fk_b13606644ece846ea2315145c4d389c2`;");
  }
}
