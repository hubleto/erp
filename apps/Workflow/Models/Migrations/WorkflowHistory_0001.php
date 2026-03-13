<?php

namespace Hubleto\App\Community\Workflow\Models\Migrations;

use Hubleto\Framework\Migration;

class WorkflowHistory_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `workflow_history`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `workflow_history` (
 `id` int(8) primary key auto_increment,
 `model` varchar(255) ,
 `record_id` int(255) ,
 `datetime_change` datetime ,
 `id_user` int(8) NULL default NULL,
 `id_workflow` int(8) NULL default NULL,
 `id_workflow_step` int(8) NULL default NULL,
 index `id` (`id`),
 INDEX `model` (`model`),
 index `record_id` (`record_id`),
 index `datetime_change` (`datetime_change`),
 index `id_user` (`id_user`),
 index `id_workflow` (`id_workflow`),
 index `id_workflow_step` (`id_workflow_step`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `workflow_history`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `workflow_history`
          ADD CONSTRAINT `fk_d341dbbd984de9084b9d1c031dd74887`
          FOREIGN KEY (`id_user`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `workflow_history`
          ADD CONSTRAINT `fk_ed0c2e346db115f96ee8ac878bcd93c5`
          FOREIGN KEY (`id_workflow`)
          REFERENCES `workflows` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `workflow_history`
          ADD CONSTRAINT `fk_530c4c876af4e6999b0e5ed4049c1dc6`
          FOREIGN KEY (`id_workflow_step`)
          REFERENCES `workflow_steps` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `workflow_history`
          DROP FOREIGN KEY `fk_d341dbbd984de9084b9d1c031dd74887`; ALTER TABLE `workflow_history`
          DROP FOREIGN KEY `fk_ed0c2e346db115f96ee8ac878bcd93c5`; ALTER TABLE `workflow_history`
          DROP FOREIGN KEY `fk_530c4c876af4e6999b0e5ed4049c1dc6`;");
  }
}