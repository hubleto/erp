<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Campaign_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_campaigns`;
      create table `email_marketing_campaigns` (
        `id` int(8) primary key auto_increment,
        `title` varchar(255),
        `target_audience` text,
        `goal` text,
        `notes` text,
        `color` char(10),
        `id_workflow` int(8) NULL default NULL,
        `id_workflow_step` int(8) NULL default NULL,
        `id_owner` int(8) NULL default NULL,
        `id_manager` int(8) NULL default NULL,
        `is_closed` int(1),
        index `id` (`id`),
        index `id_workflow` (`id_workflow`),
        index `id_workflow_step` (`id_workflow_step`),
        index `id_owner` (`id_owner`),
        index `id_manager` (`id_manager`),
        index `is_closed` (`is_closed`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_campaigns`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_campaigns` ADD CONSTRAINT `fk__email_marketing_campaigns__id_workflow`
      FOREIGN KEY (`id_workflow`) REFERENCES `workflows` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `email_marketing_campaigns` ADD CONSTRAINT `fk__email_marketing_campaigns__id_workflow_step`
      FOREIGN KEY (`id_workflow_step`) REFERENCES `workflow_steps` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `email_marketing_campaigns` ADD CONSTRAINT `fk__email_marketing_campaigns__id_owner`
      FOREIGN KEY (`id_owner`) REFERENCES `users` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `email_marketing_campaigns` ADD CONSTRAINT `fk__email_marketing_campaigns__id_manager`
      FOREIGN KEY (`id_manager`) REFERENCES `users` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_campaigns` DROP FOREIGN KEY `fk__email_marketing_campaigns__id_workflow`; 
      ALTER TABLE `email_marketing_campaigns` DROP FOREIGN KEY `fk__email_marketing_campaigns__id_workflow_step`; 
      ALTER TABLE `email_marketing_campaigns` DROP FOREIGN KEY `fk__email_marketing_campaigns__id_owner`; 
      ALTER TABLE `email_marketing_campaigns` DROP FOREIGN KEY `fk__email_marketing_campaigns__id_manager`; 
    ");
  }
}