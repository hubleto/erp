<?php

namespace Hubleto\App\Community\LinkedinMessages\Models\Migrations;

use Hubleto\Framework\Migration;

class Profile_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `linkedin_profiles`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `linkedin_profiles` (
 `id` int(8) primary key auto_increment,
 `id_owner` int(8) NULL default NULL,
 `linkedin_urn` varchar(255) ,
 `first_name` varchar(255) ,
 `last_name` varchar(255) ,
 `headline` varchar(255) ,
 `company` varchar(255) ,
 `location` varchar(255) ,
 `email` varchar(255) ,
 `profile_url` varchar(255) ,
 `picture_url` varchar(255) ,
 `note` text ,
 `date_created` datetime ,
 index `id` (`id`),
 index `id_owner` (`id_owner`),
 index `date_created` (`date_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `linkedin_profiles`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `linkedin_profiles`
          ADD CONSTRAINT `fk_be90f4e67efeddfc7a4edfb3a4ad5a34`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `linkedin_profiles`
          DROP FOREIGN KEY `fk_be90f4e67efeddfc7a4edfb3a4ad5a34`;");
  }
}
