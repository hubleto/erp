<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Email_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_emails`;
      create table `email_marketing_emails` (
        `id` int(8) primary key auto_increment,
        `uid` varchar(255),
        `name` varchar(255),
        `utm_source` varchar(255),
        `utm_campaign` varchar(255),
        `utm_term` varchar(255),
        `utm_content` varchar(255),
        `target_audience` text,
        `goal` text,
        `notes` text,
        `shared_folder` varchar(255),
        `color` char(10),
        `id_sender_account` int(8) NULL default NULL,
        `mail_subject` varchar(255),
        `mail_body` text,
        `reply_to` varchar(255),
        `id_workflow` int(8) NULL default NULL,
        `id_workflow_step` int(8) NULL default NULL,
        `id_owner` int(8) NULL default NULL,
        `id_manager` int(8) NULL default NULL,
        `shared_with` text,
        `is_approved` int(1),
        `is_closed` int(1),
        `datetime_created` datetime,
        `id_launched_by` int(8) NULL default NULL,
        `datetime_launched` datetime,
        index `id` (`id`),
        index `id_sender_account` (`id_sender_account`),
        index `id_workflow` (`id_workflow`),
        index `id_workflow_step` (`id_workflow_step`),
        index `id_owner` (`id_owner`),
        index `id_manager` (`id_manager`),
        index `is_approved` (`is_approved`),
        index `is_closed` (`is_closed`),
        index `datetime_created` (`datetime_created`),
        index `id_launched_by` (`id_launched_by`),
        index `datetime_launched` (`datetime_launched`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_emails`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_emails` ADD CONSTRAINT `fk__email_marketing_emails__id_sender_account`
      FOREIGN KEY (`id_sender_account`) REFERENCES `mails_accounts` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
          
      ALTER TABLE `email_marketing_emails` ADD CONSTRAINT `fk__email_marketing_emails__id_workflow`
      FOREIGN KEY (`id_workflow`) REFERENCES `workflows` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `email_marketing_emails` ADD CONSTRAINT `fk__email_marketing_emails__id_workflow_step`
      FOREIGN KEY (`id_workflow_step`) REFERENCES `workflow_steps` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `email_marketing_emails` ADD CONSTRAINT `fk__email_marketing_emails__id_owner`
      FOREIGN KEY (`id_owner`) REFERENCES `users` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `email_marketing_emails` ADD CONSTRAINT `fk__email_marketing_emails__id_manager`
      FOREIGN KEY (`id_manager`) REFERENCES `users` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `email_marketing_emails` ADD CONSTRAINT `fk__email_marketing_emails__id_launched_by`
      FOREIGN KEY (`id_launched_by`) REFERENCES `users` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_emails` DROP FOREIGN KEY `fk__email_marketing_emails__id_sender_account`; 
      ALTER TABLE `email_marketing_emails` DROP FOREIGN KEY `fk__email_marketing_emails__id_workflow`; 
      ALTER TABLE `email_marketing_emails` DROP FOREIGN KEY `fk__email_marketing_emails__id_workflow_step`; 
      ALTER TABLE `email_marketing_emails` DROP FOREIGN KEY `fk__email_marketing_emails__id_owner`; 
      ALTER TABLE `email_marketing_emails` DROP FOREIGN KEY `fk__email_marketing_emails__id_manager`; 
      ALTER TABLE `email_marketing_emails` DROP FOREIGN KEY `fk__email_marketing_emails__id_launched_by`; 
    ");
  }
}