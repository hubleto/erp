<?php

namespace Hubleto\App\Community\Products\Models\Migrations;

use Hubleto\Framework\Migration;

class Unit_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `product_units`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `product_units` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `category` int(255) ,
 index `id` (`id`),
 index `category` (`category`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `product_units`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {

  }

  public function downgradeForeignKeys(): void
  {

  }
}
