<?php

namespace Hubleto\App\Community\CalendarSync\Models\Migrations;

use Hubleto\Framework\Migration;

class Source_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `calendar_sync_sources`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `calendar_sync_sources` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `link` varchar(255) ,
 `type` varchar(255) ,
 `color` char(10) ,
 `active` int(1) ,
 index `id` (`id`),
 index `active` (`active`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `calendar_sync_sources`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}