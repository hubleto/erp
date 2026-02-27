<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class CampaignTask_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `campaigns_tasks`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `campaigns_tasks` (
 `id` int(8) primary key auto_increment,
 `id_campaign` int(8) NULL default NULL,
 `id_task` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_campaign` (`id_campaign`),
 index `id_task` (`id_task`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `campaigns_tasks`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `campaigns_tasks`
          ADD CONSTRAINT `fk_dbaf10e04d687615229e97f63b29697b`
          FOREIGN KEY (`id_campaign`)
          REFERENCES `campaigns` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `campaigns_tasks`
          ADD CONSTRAINT `fk_d92ed92bc7b53d7cfb4ba3090eba9a60`
          FOREIGN KEY (`id_task`)
          REFERENCES `tasks` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `campaigns_tasks`
          DROP FOREIGN KEY `fk_dbaf10e04d687615229e97f63b29697b`; ALTER TABLE `campaigns_tasks`
          DROP FOREIGN KEY `fk_d92ed92bc7b53d7cfb4ba3090eba9a60`;");
  }
}