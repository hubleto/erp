<?php

namespace Hubleto\App\Community\Auth\Models\Migrations;

use Hubleto\Framework\Migration;

class Token_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `tokens`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `tokens` (
 `id` int(8) primary key auto_increment,
 `type` varchar(255) ,
 `valid_to` datetime ,
 `token` varchar(255) ,
 index `id` (`id`),
 index `valid_to` (`valid_to`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;


              alter table `tokens`
              add index `uid` (`token` asc)
            ;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `tokens`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}