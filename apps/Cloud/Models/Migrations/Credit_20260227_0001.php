<?php

namespace Hubleto\App\Community\Cloud\Models\Migrations;

use Hubleto\Framework\Migration;

class Credit_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cloud_credit`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cloud_credit` (
 `id` int(8) primary key auto_increment,
 `datetime_recalculated` datetime ,
 `credit` decimal(14, 4) ,
 index `id` (`id`),
 index `datetime_recalculated` (`datetime_recalculated`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cloud_credit`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    
  }

  public function uninstallForeignKeys(): void
  {
    
  }
}