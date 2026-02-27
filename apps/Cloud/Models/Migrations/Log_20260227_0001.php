<?php

namespace Hubleto\App\Community\Cloud\Models\Migrations;

use Hubleto\Framework\Migration;

class Log_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cloud_log`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cloud_log` (
 `id` int(8) primary key auto_increment,
 `log_datetime` datetime ,
 `active_users` int(255) ,
 `paid_apps` int(255) ,
 `is_premium_expected` int(1) ,
 index `id` (`id`),
 index `log_datetime` (`log_datetime`),
 index `active_users` (`active_users`),
 index `paid_apps` (`paid_apps`),
 index `is_premium_expected` (`is_premium_expected`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cloud_log`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    
  }

  public function uninstallForeignKeys(): void
  {
    
  }
}