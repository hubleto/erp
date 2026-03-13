<?php

namespace Hubleto\App\Community\Shops\Models\Migrations;

use Hubleto\Framework\Migration;

class Shop_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `shops`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `shops` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `color` char(10) ,
 `address` varchar(255) ,
 `short_description` text ,
 `long_description` text ,
 `photo_1` varchar(255) ,
 `photo_2` varchar(255) ,
 `photo_3` varchar(255) ,
 `photo_4` varchar(255) ,
 `photo_5` varchar(255) ,
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `shops`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}