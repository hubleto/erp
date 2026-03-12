<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class DealTask_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deals_tasks`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `deals_tasks` (
 `id` int(8) primary key auto_increment,
 `id_deal` int(8) NULL default NULL,
 `id_task` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_deal` (`id_deal`),
 index `id_task` (`id_task`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deals_tasks`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deals_tasks`
          ADD CONSTRAINT `fk_e7ea88ba9e398a2c9be8cb9f1a8dedab`
          FOREIGN KEY (`id_deal`)
          REFERENCES `deals` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deals_tasks`
          ADD CONSTRAINT `fk_4961fc696f83fb851cd40236cdc76868`
          FOREIGN KEY (`id_task`)
          REFERENCES `tasks` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deals_tasks`
          DROP FOREIGN KEY `fk_e7ea88ba9e398a2c9be8cb9f1a8dedab`; ALTER TABLE `deals_tasks`
          DROP FOREIGN KEY `fk_4961fc696f83fb851cd40236cdc76868`;");
  }
}