<?php

namespace Hubleto\App\Community\Workflow\Models\Migrations;

use Hubleto\Framework\Migration;

class WorkflowStep_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `workflow_steps`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `workflow_steps` (
 `id` int(8) primary key auto_increment,
 `id_workflow` int(8) NULL default NULL,
 `name` varchar(255) ,
 `order` int(255) ,
 `color` char(10) ,
 `tag` varchar(255) ,
 `probability` int(255) ,
 index `id` (`id`),
 index `id_workflow` (`id_workflow`),
 index `order` (`order`),
 index `probability` (`probability`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `workflow_steps`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `workflow_steps`
          ADD CONSTRAINT `fk_fabc826acfa6488d209bc1f03431b9ef`
          FOREIGN KEY (`id_workflow`)
          REFERENCES `workflows` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `workflow_steps`
          DROP FOREIGN KEY `fk_fabc826acfa6488d209bc1f03431b9ef`;");
  }
}