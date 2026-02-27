<?php

namespace Hubleto\App\Community\Warehouses\Models\Migrations;

use Hubleto\Framework\Migration;

class LocationType_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses_locations_types`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `warehouses_locations_types` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses_locations_types`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    
  }

  public function uninstallForeignKeys(): void
  {
    
  }
}