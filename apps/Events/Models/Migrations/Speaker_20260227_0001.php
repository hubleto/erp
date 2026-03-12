<?php

namespace Hubleto\App\Community\Events\Models\Migrations;

use Hubleto\Framework\Migration;

class Speaker_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_speakers`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `events_speakers` (
 `id` int(8) primary key auto_increment,
 `salutation` varchar(255) ,
 `title_before` varchar(255) ,
 `full_name` varchar(255) ,
 `title_after` varchar(255) ,
 `short_bio` varchar(255) ,
 `long_bio` text ,
 `email` varchar(255) ,
 `phone` varchar(255) ,
 `social_profile_url_1` varchar(255) ,
 `social_profile_url_2` varchar(255) ,
 `social_profile_url_3` varchar(255) ,
 `social_profile_url_4` varchar(255) ,
 `social_profile_url_5` varchar(255) ,
 `notes` varchar(255) ,
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `events_speakers`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}