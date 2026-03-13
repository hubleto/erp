<?php

namespace Hubleto\App\Community\Usage\Models\Migrations;

use Hubleto\Framework\Migration;

class Log_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `usage_log`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `usage_log` (
 `id` int(8) primary key auto_increment,
 `datetime` datetime ,
 `ip` varchar(255) ,
 `route` varchar(255) ,
 `params` varchar(255) ,
 `message` varchar(255) ,
 `id_user` int(8) NULL default NULL,
 index `id` (`id`),
 index `datetime` (`datetime`),
 index `id_user` (`id_user`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `usage_log`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `usage_log`
          ADD CONSTRAINT `fk_c9a8f81fafb971ee5acbd5d62a6afa63`
          FOREIGN KEY (`id_user`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `usage_log`
          DROP FOREIGN KEY `fk_c9a8f81fafb971ee5acbd5d62a6afa63`;");
  }
}