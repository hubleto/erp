<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Click_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `campaigns_clicks`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `campaigns_clicks` (
 `id` int(8) primary key auto_increment,
 `id_campaign` int(8) NULL default NULL,
 `id_recipient` int(8) NULL default NULL,
 `url` varchar(255) ,
 `datetime_clicked` datetime ,
 index `id` (`id`),
 index `id_campaign` (`id_campaign`),
 index `id_recipient` (`id_recipient`),
 index `datetime_clicked` (`datetime_clicked`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `campaigns_clicks`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `campaigns_clicks`
          ADD CONSTRAINT `fk_25f2d97edae0c162eb5c84ff35ac5015`
          FOREIGN KEY (`id_campaign`)
          REFERENCES `campaigns` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `campaigns_clicks`
          ADD CONSTRAINT `fk_57ec6472e2b22b2b2285556ecb141fba`
          FOREIGN KEY (`id_recipient`)
          REFERENCES `campaigns_recipients` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `campaigns_clicks`
          DROP FOREIGN KEY `fk_25f2d97edae0c162eb5c84ff35ac5015`; ALTER TABLE `campaigns_clicks`
          DROP FOREIGN KEY `fk_57ec6472e2b22b2b2285556ecb141fba`;");
  }
}