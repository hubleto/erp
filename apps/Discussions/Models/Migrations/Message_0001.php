<?php

namespace Hubleto\App\Community\Discussions\Models\Migrations;

use Hubleto\Framework\Migration;

class Message_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `discussions_messages`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `discussions_messages` (
 `id` int(8) primary key auto_increment,
 `id_discussion` int(8) NULL default NULL,
 `id_from` int(8) NULL default NULL,
 `from_email` varchar(255) ,
 `message` text ,
 `sent` datetime ,
 index `id` (`id`),
 index `id_discussion` (`id_discussion`),
 index `id_from` (`id_from`),
 index `sent` (`sent`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `discussions_messages`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `discussions_messages`
          ADD CONSTRAINT `fk_4b01968079d627e0151024026609a533`
          FOREIGN KEY (`id_discussion`)
          REFERENCES `discussions` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `discussions_messages`
          ADD CONSTRAINT `fk_aef3201392c94ff172f24d110e96ac3d`
          FOREIGN KEY (`id_from`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `discussions_messages`
          DROP FOREIGN KEY `fk_4b01968079d627e0151024026609a533`; ALTER TABLE `discussions_messages`
          DROP FOREIGN KEY `fk_aef3201392c94ff172f24d110e96ac3d`;");
  }
}