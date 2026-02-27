<?php

namespace Hubleto\App\Community\Auth\Models\Migrations;

use Hubleto\Framework\Migration;

class UserRole_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `user_roles`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `user_roles` (
 `id` int(8) primary key auto_increment,
 `role` varchar(255) ,
 `grant_all` int(1) ,
 `description` varchar(255) ,
 `is_default` int(1) ,
 `permissions` text ,
 index `id` (`id`),
 index `grant_all` (`grant_all`),
 index `is_default` (`is_default`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `user_roles`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    
  }

  public function uninstallForeignKeys(): void
  {
    
  }
}