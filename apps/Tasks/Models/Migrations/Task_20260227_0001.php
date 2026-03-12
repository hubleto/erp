<?php

namespace Hubleto\App\Community\Tasks\Models\Migrations;

use Hubleto\Framework\Migration;

class Task_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `tasks`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `tasks` (
 `id` int(8) primary key auto_increment,
 `identifier` varchar(255) ,
 `title` varchar(255) ,
 `description` text ,
 `id_customer` int(8) NULL default NULL,
 `id_contact` int(8) NULL default NULL,
 `id_developer` int(8) NULL default NULL,
 `id_tester` int(8) NULL default NULL,
 `priority` int(255) ,
 `hours_estimation` decimal(14, 2) ,
 `duration_days` int(255) ,
 `date_start` date ,
 `date_deadline` date ,
 `id_workflow` int(8) NULL default NULL,
 `id_workflow_step` int(8) NULL default NULL,
 `is_chargeable` int(1) ,
 `is_milestone` int(1) ,
 `is_closed` int(1) ,
 `shared_folder` varchar(255) ,
 `notes` text ,
 `date_created` datetime ,
 index `id` (`id`),
 index `id_customer` (`id_customer`),
 index `id_contact` (`id_contact`),
 index `id_developer` (`id_developer`),
 index `id_tester` (`id_tester`),
 index `priority` (`priority`),
 index `duration_days` (`duration_days`),
 index `date_start` (`date_start`),
 index `date_deadline` (`date_deadline`),
 index `id_workflow` (`id_workflow`),
 index `id_workflow_step` (`id_workflow_step`),
 index `is_chargeable` (`is_chargeable`),
 index `is_milestone` (`is_milestone`),
 index `is_closed` (`is_closed`),
 index `date_created` (`date_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `tasks`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `tasks`
          ADD CONSTRAINT `fk_1ea0b29e97f635aa54362b9eca349740`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `tasks`
          ADD CONSTRAINT `fk_8911b4bf2fe9d71c33324a4dcabacad5`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `tasks`
          ADD CONSTRAINT `fk_fdbfa05ee7e116d6057ea28483e3b7b8`
          FOREIGN KEY (`id_developer`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `tasks`
          ADD CONSTRAINT `fk_24e53818d87fc13663b38a15d9ae43ed`
          FOREIGN KEY (`id_tester`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `tasks`
          ADD CONSTRAINT `fk_4cad3b24548d6d8ef1affe0e784c1d15`
          FOREIGN KEY (`id_workflow`)
          REFERENCES `workflows` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `tasks`
          ADD CONSTRAINT `fk_c7940d8cd34669ccf791ec0e3311079d`
          FOREIGN KEY (`id_workflow_step`)
          REFERENCES `workflow_steps` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `tasks`
          DROP FOREIGN KEY `fk_1ea0b29e97f635aa54362b9eca349740`; ALTER TABLE `tasks`
          DROP FOREIGN KEY `fk_8911b4bf2fe9d71c33324a4dcabacad5`; ALTER TABLE `tasks`
          DROP FOREIGN KEY `fk_fdbfa05ee7e116d6057ea28483e3b7b8`; ALTER TABLE `tasks`
          DROP FOREIGN KEY `fk_24e53818d87fc13663b38a15d9ae43ed`; ALTER TABLE `tasks`
          DROP FOREIGN KEY `fk_4cad3b24548d6d8ef1affe0e784c1d15`; ALTER TABLE `tasks`
          DROP FOREIGN KEY `fk_c7940d8cd34669ccf791ec0e3311079d`;");
  }
}