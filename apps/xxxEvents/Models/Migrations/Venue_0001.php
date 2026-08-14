<?php

namespace Hubleto\App\Community\Events\Models\Migrations;

use Hubleto\Framework\Migration;

class Venue_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_venues`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `events_venues` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `address` varchar(255) ,
 `address_plus_code` varchar(255) ,
 `contact_person` varchar(255) ,
 `contact_email` varchar(255) ,
 `contact_phone` varchar(255) ,
 `lng` decimal(14, 4) ,
 `lat` decimal(14, 4) ,
 `description` text ,
 `capacity` decimal(14, 4) ,
 `photo_1` varchar(255) ,
 `photo_2` varchar(255) ,
 `photo_3` varchar(255) ,
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_venues`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}