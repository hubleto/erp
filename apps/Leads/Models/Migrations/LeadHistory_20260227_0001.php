<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

class LeadHistory_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `lead_histories`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `lead_histories` (
 `id` int(8) primary key auto_increment,
 `change_date` date ,
 `id_lead` int(8) NULL default NULL,
 `description` text ,
 index `id` (`id`),
 index `change_date` (`change_date`),
 index `id_lead` (`id_lead`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `lead_histories`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `lead_histories`
          ADD CONSTRAINT `fk_c2575eb82fed1bd49b9cbe99ccf61902`
          FOREIGN KEY (`id_lead`)
          REFERENCES `leads` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `lead_histories`
          DROP FOREIGN KEY `fk_c2575eb82fed1bd49b9cbe99ccf61902`;");
  }
}