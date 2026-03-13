<?php

namespace Hubleto\App\Community\Tasks\Models\Migrations;

use Hubleto\Framework\Migration;

class Todo_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `tasks_todo`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `tasks_todo` (
 `id` int(8) primary key auto_increment,
 `todo` varchar(255) ,
 `id_task` int(8) NULL default NULL,
 `id_responsible` int(8) NULL default NULL,
 `is_closed` int(1) ,
 `date_deadline` date ,
 index `id` (`id`),
 index `id_task` (`id_task`),
 index `id_responsible` (`id_responsible`),
 index `is_closed` (`is_closed`),
 index `date_deadline` (`date_deadline`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `tasks_todo`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `tasks_todo`
          ADD CONSTRAINT `fk_14180a3b106b417853476dc5bd0b5e70`
          FOREIGN KEY (`id_task`)
          REFERENCES `tasks` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `tasks_todo`
          ADD CONSTRAINT `fk_29af1e1351c1f958bf7d85cbfc0a7fc0`
          FOREIGN KEY (`id_responsible`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `tasks_todo`
          DROP FOREIGN KEY `fk_14180a3b106b417853476dc5bd0b5e70`; ALTER TABLE `tasks_todo`
          DROP FOREIGN KEY `fk_29af1e1351c1f958bf7d85cbfc0a7fc0`;");
  }
}