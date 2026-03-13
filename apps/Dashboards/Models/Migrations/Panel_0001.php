<?php

namespace Hubleto\App\Community\Dashboards\Models\Migrations;

use Hubleto\Framework\Migration;

class Panel_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `dashboards_panels`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `dashboards_panels` (
 `id` int(8) primary key auto_increment,
 `id_dashboard` int(8) NULL default NULL,
 `board_url_slug` varchar(255) ,
 `title` varchar(255) ,
 `width` int(255) ,
 `position` int(255) ,
 `configuration` text ,
 index `id` (`id`),
 index `id_dashboard` (`id_dashboard`),
 index `width` (`width`),
 index `position` (`position`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `dashboards_panels`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `dashboards_panels`
          ADD CONSTRAINT `fk_cc0608561cc277c542bca13716847dd0`
          FOREIGN KEY (`id_dashboard`)
          REFERENCES `dashboards` (`id`)
          ON DELETE CASCADE
          ON UPDATE CASCADE;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `dashboards_panels`
          DROP FOREIGN KEY `fk_cc0608561cc277c542bca13716847dd0`;");
  }
}