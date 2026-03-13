<?php

namespace Hubleto\App\Community\Auth\Models\Migrations;

use Hubleto\Framework\Migration;

class User_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `users`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `users` (
 `id` int(8) primary key auto_increment,
 `email` varchar(255) ,
 `login` varchar(255) ,
 `password` varchar(255) ,
 `type` int(255) ,
 `is_active` int(1) ,
 `last_login_time` datetime ,
 `last_login_ip` varchar(255) ,
 `last_access_time` datetime ,
 `last_access_ip` varchar(255) ,
 `first_name` varchar(255) ,
 `last_name` varchar(255) ,
 `nick` varchar(255) ,
 `position` varchar(255) ,
 `phone_1` varchar(255) ,
 `phone_2` varchar(255) ,
 `photo` varchar(255) ,
 `language` varchar(255) ,
 `timezone` varchar(255) ,
 `id_default_company` int(8) NULL default NULL,
 `apps` text ,
 `permissions` text ,
 index `id` (`id`),
 index `type` (`type`),
 index `is_active` (`is_active`),
 index `last_login_time` (`last_login_time`),
 index `last_access_time` (`last_access_time`),
 index `id_default_company` (`id_default_company`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;


              alter table `users`
              add constraint `login` unique (`login` asc)
            ;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `users`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `users`
          ADD CONSTRAINT `fk_d00cabf34a363fcdf1d7485c331eb1f1`
          FOREIGN KEY (`id_default_company`)
          REFERENCES `companies` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `users`
          DROP FOREIGN KEY `fk_d00cabf34a363fcdf1d7485c331eb1f1`;");
  }
}