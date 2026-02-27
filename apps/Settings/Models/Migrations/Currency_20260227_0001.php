<?php

namespace Hubleto\App\Community\Settings\Models\Migrations;

use Hubleto\Framework\Migration;

class Currency_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `currencies`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `currencies` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `code` varchar(255) ,
 `symbol` varchar(255) ,
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `currencies`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    
  }

  public function uninstallForeignKeys(): void
  {
    
  }
}