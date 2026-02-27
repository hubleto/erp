<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class DealTag_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cross_deal_tags`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cross_deal_tags` (
 `id` int(8) primary key auto_increment,
 `id_deal` int(8) NULL default NULL,
 `id_tag` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_deal` (`id_deal`),
 index `id_tag` (`id_tag`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cross_deal_tags`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cross_deal_tags`
          ADD CONSTRAINT `fk_7893ade181311401956233baa244ebca`
          FOREIGN KEY (`id_deal`)
          REFERENCES `deals` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `cross_deal_tags`
          ADD CONSTRAINT `fk_af71c8cd58d0b5632e54373f9ee90950`
          FOREIGN KEY (`id_tag`)
          REFERENCES `deal_tags` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cross_deal_tags`
          DROP FOREIGN KEY `fk_7893ade181311401956233baa244ebca`; ALTER TABLE `cross_deal_tags`
          DROP FOREIGN KEY `fk_af71c8cd58d0b5632e54373f9ee90950`;");
  }
}