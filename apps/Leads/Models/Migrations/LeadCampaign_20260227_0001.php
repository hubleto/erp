<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

class LeadCampaign_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `leads_campaigns`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `leads_campaigns` (
 `id` int(8) primary key auto_increment,
 `id_lead` int(8) NULL default NULL,
 `id_campaign` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_lead` (`id_lead`),
 index `id_campaign` (`id_campaign`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `leads_campaigns`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `leads_campaigns`
          ADD CONSTRAINT `fk_cf480e86dbefdc1d4a4409fd06102978`
          FOREIGN KEY (`id_lead`)
          REFERENCES `leads` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `leads_campaigns`
          ADD CONSTRAINT `fk_e9766c27fffec1fbd1a4e235cd5f77eb`
          FOREIGN KEY (`id_campaign`)
          REFERENCES `campaigns` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `leads_campaigns`
          DROP FOREIGN KEY `fk_cf480e86dbefdc1d4a4409fd06102978`; ALTER TABLE `leads_campaigns`
          DROP FOREIGN KEY `fk_e9766c27fffec1fbd1a4e235cd5f77eb`;");
  }
}