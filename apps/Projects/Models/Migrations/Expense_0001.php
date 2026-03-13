<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class Expense_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_expenses`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `projects_expenses` (
 `id` int(8) primary key auto_increment,
 `id_project` int(8) NULL default NULL,
 `reason` varchar(255) ,
 `date` date ,
 `amount` decimal(14, 4) ,
 `id_approved_by` int(8) NULL default NULL,
 `id_spent_by` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_project` (`id_project`),
 index `date` (`date`),
 index `id_approved_by` (`id_approved_by`),
 index `id_spent_by` (`id_spent_by`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_expenses`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_expenses`
          ADD CONSTRAINT `fk_741cd33b435694947f9d5d5a9fbf688f`
          FOREIGN KEY (`id_project`)
          REFERENCES `projects` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects_expenses`
          ADD CONSTRAINT `fk_10a0007a20b06acd06b8979aaecc390d`
          FOREIGN KEY (`id_approved_by`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects_expenses`
          ADD CONSTRAINT `fk_594ff3c0730dde3c2bd7656634bb2e78`
          FOREIGN KEY (`id_spent_by`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_expenses`
          DROP FOREIGN KEY `fk_741cd33b435694947f9d5d5a9fbf688f`; ALTER TABLE `projects_expenses`
          DROP FOREIGN KEY `fk_10a0007a20b06acd06b8979aaecc390d`; ALTER TABLE `projects_expenses`
          DROP FOREIGN KEY `fk_594ff3c0730dde3c2bd7656634bb2e78`;");
  }
}