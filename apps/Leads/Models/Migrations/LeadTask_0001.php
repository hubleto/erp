<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

class LeadTask_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `leads_tasks`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `leads_tasks` (
 `id` int(8) primary key auto_increment,
 `id_lead` int(8) NULL default NULL,
 `id_task` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_lead` (`id_lead`),
 index `id_task` (`id_task`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `leads_tasks`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `leads_tasks`
          ADD CONSTRAINT `fk_cb1f2c2b41c88837173a2bb617b18938`
          FOREIGN KEY (`id_lead`)
          REFERENCES `leads` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `leads_tasks`
          ADD CONSTRAINT `fk_6b03ce561b3d7d3538c9c1d0dfad818a`
          FOREIGN KEY (`id_task`)
          REFERENCES `tasks` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `leads_tasks`
          DROP FOREIGN KEY `fk_cb1f2c2b41c88837173a2bb617b18938`; ALTER TABLE `leads_tasks`
          DROP FOREIGN KEY `fk_6b03ce561b3d7d3538c9c1d0dfad818a`;");
  }
}