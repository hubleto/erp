<?php

namespace Hubleto\App\Community\Api\Models\Migrations;

use Hubleto\Framework\Migration;

class Usage_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `api_usages`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `api_usages` (
 `id` int(8) primary key auto_increment,
 `id_key` int(8) NULL default NULL,
 `app` varchar(255) ,
 `controller` varchar(255) ,
 `used_on` datetime ,
 `ip_address` varchar(255) ,
 `status` varchar(255) ,
 index `id` (`id`),
 index `id_key` (`id_key`),
 INDEX `app` (`app`),
 INDEX `controller` (`controller`),
 index `used_on` (`used_on`),
 INDEX `ip_address` (`ip_address`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `api_usages`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `api_usages`
          ADD CONSTRAINT `fk_9d562afc184bc1da84b9598e77d2cb9e`
          FOREIGN KEY (`id_key`)
          REFERENCES `api_keys` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `api_usages`
          DROP FOREIGN KEY `fk_9d562afc184bc1da84b9598e77d2cb9e`;");
  }
}