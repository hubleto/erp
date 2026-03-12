<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class LostReason_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deal_lost_reasons`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `deal_lost_reasons` (
 `id` int(8) primary key auto_increment,
 `reason` varchar(255) ,
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deal_lost_reasons`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}