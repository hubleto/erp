<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class DealLead_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deals_leads`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `deals_leads` (
 `id` int(8) primary key auto_increment,
 `id_deal` int(8) NULL default NULL,
 `id_lead` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_deal` (`id_deal`),
 index `id_lead` (`id_lead`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deals_leads`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deals_leads`
          ADD CONSTRAINT `fk_f633d64bbe0851841dab5d0a9e6faa94`
          FOREIGN KEY (`id_deal`)
          REFERENCES `deals` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deals_leads`
          ADD CONSTRAINT `fk_c42ba0b61ad58f2357c564c13a225337`
          FOREIGN KEY (`id_lead`)
          REFERENCES `leads` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deals_leads`
          DROP FOREIGN KEY `fk_f633d64bbe0851841dab5d0a9e6faa94`; ALTER TABLE `deals_leads`
          DROP FOREIGN KEY `fk_c42ba0b61ad58f2357c564c13a225337`;");
  }
}