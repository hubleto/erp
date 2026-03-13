<?php

namespace Hubleto\App\Community\Settings\Models\Migrations;

use Hubleto\Framework\Migration;

class Company_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `companies`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `companies` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `registration_id` varchar(255) ,
 `business_register` varchar(255) ,
 `tax_id` varchar(255) ,
 `vat_id` varchar(255) ,
 `street_1` varchar(255) ,
 `street_2` varchar(255) ,
 `zip` varchar(255) ,
 `city` varchar(255) ,
 `region` varchar(255) ,
 `country` varchar(255) ,
 `logo` varchar(255) ,
 `brand_color_primary` char(10) ,
 `brand_color_secondary` char(10) ,
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `companies`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}