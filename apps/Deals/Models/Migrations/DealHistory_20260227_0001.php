<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class DealHistory_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deal_histories`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `deal_histories` (
 `id` int(8) primary key auto_increment,
 `change_date` date ,
 `id_deal` int(8) NULL default NULL,
 `description` varchar(255) ,
 index `id` (`id`),
 index `change_date` (`change_date`),
 index `id_deal` (`id_deal`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deal_histories`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deal_histories`
          ADD CONSTRAINT `fk_7581ba96cf7d146df3487f100865f31b`
          FOREIGN KEY (`id_deal`)
          REFERENCES `deals` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deal_histories`
          DROP FOREIGN KEY `fk_7581ba96cf7d146df3487f100865f31b`;");
  }
}