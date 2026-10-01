<?php

namespace Hubleto\App\Custom\LinkedinMessages\Models\Migrations;

use Hubleto\Framework\Migration;

class Campaign_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `linkedin_campaigns`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `linkedin_campaigns` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `target` text ,
 `goal` text ,
 `id_owner` int(8) NULL default NULL,
 `id_workflow` int(8) NULL default NULL,
 `id_workflow_step` int(8) NULL default NULL,
 `is_closed` int(1) ,
 `date_created` datetime ,
 index `id` (`id`),
 index `id_owner` (`id_owner`),
 index `id_workflow` (`id_workflow`),
 index `id_workflow_step` (`id_workflow_step`),
 index `is_closed` (`is_closed`),
 index `date_created` (`date_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `linkedin_campaigns`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `linkedin_campaigns`
          ADD CONSTRAINT `fk_77ad64927b9d21708e94a584ddd04199`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `linkedin_campaigns`
          ADD CONSTRAINT `fk_76f0c7e5afc2eb55fbdb0e8ca1381e58`
          FOREIGN KEY (`id_workflow`)
          REFERENCES `workflows` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `linkedin_campaigns`
          ADD CONSTRAINT `fk_929b1acf51670581510bd0b9bccc366a`
          FOREIGN KEY (`id_workflow_step`)
          REFERENCES `workflow_steps` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `linkedin_campaigns`
          DROP FOREIGN KEY `fk_77ad64927b9d21708e94a584ddd04199`; ALTER TABLE `linkedin_campaigns`
          DROP FOREIGN KEY `fk_76f0c7e5afc2eb55fbdb0e8ca1381e58`; ALTER TABLE `linkedin_campaigns`
          DROP FOREIGN KEY `fk_929b1acf51670581510bd0b9bccc366a`;");
  }
}
