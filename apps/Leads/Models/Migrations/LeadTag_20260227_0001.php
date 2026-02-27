<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

class LeadTag_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cross_lead_tags`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cross_lead_tags` (
 `id` int(8) primary key auto_increment,
 `id_lead` int(8) NULL default NULL,
 `id_tag` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_lead` (`id_lead`),
 index `id_tag` (`id_tag`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cross_lead_tags`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cross_lead_tags`
          ADD CONSTRAINT `fk_0cf287ed9ea7a5ea64d06d603e01f12e`
          FOREIGN KEY (`id_lead`)
          REFERENCES `leads` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `cross_lead_tags`
          ADD CONSTRAINT `fk_a6eeb7a8de2bcde0892dcb956374de98`
          FOREIGN KEY (`id_tag`)
          REFERENCES `lead_tags` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cross_lead_tags`
          DROP FOREIGN KEY `fk_0cf287ed9ea7a5ea64d06d603e01f12e`; ALTER TABLE `cross_lead_tags`
          DROP FOREIGN KEY `fk_a6eeb7a8de2bcde0892dcb956374de98`;");
  }
}