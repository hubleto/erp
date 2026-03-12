<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Campaign_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `campaigns`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `campaigns` (
 `id` int(8) primary key auto_increment,
 `uid` varchar(255) ,
 `name` varchar(255) ,
 `utm_source` varchar(255) ,
 `utm_campaign` varchar(255) ,
 `utm_term` varchar(255) ,
 `utm_content` varchar(255) ,
 `target_audience` text ,
 `goal` text ,
 `notes` text ,
 `shared_folder` varchar(255) ,
 `color` char(10) ,
 `id_mail_account` int(8) NULL default NULL,
 `id_mail_template` int(8) NULL default NULL,
 `reply_to` varchar(255) ,
 `id_workflow` int(8) NULL default NULL,
 `id_workflow_step` int(8) NULL default NULL,
 `id_owner` int(8) NULL default NULL,
 `id_manager` int(8) NULL default NULL,
 `shared_with` text ,
 `is_approved` int(1) ,
 `is_closed` int(1) ,
 `datetime_created` datetime ,
 `id_launched_by` int(8) NULL default NULL,
 `datetime_launched` datetime ,
 index `id` (`id`),
 index `id_mail_account` (`id_mail_account`),
 index `id_mail_template` (`id_mail_template`),
 index `id_workflow` (`id_workflow`),
 index `id_workflow_step` (`id_workflow_step`),
 index `id_owner` (`id_owner`),
 index `id_manager` (`id_manager`),
 index `is_approved` (`is_approved`),
 index `is_closed` (`is_closed`),
 index `datetime_created` (`datetime_created`),
 index `id_launched_by` (`id_launched_by`),
 index `datetime_launched` (`datetime_launched`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `campaigns`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `campaigns`
          ADD CONSTRAINT `fk_a588511694e8c9d43e6afa9d3e84a036`
          FOREIGN KEY (`id_mail_account`)
          REFERENCES `mails_accounts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `campaigns`
          ADD CONSTRAINT `fk_c27abb2d87accd8d73befc05de085a93`
          FOREIGN KEY (`id_mail_template`)
          REFERENCES `mails_templates` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `campaigns`
          ADD CONSTRAINT `fk_56100642a935b1f9d63eaef207abb7ef`
          FOREIGN KEY (`id_workflow`)
          REFERENCES `workflows` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `campaigns`
          ADD CONSTRAINT `fk_0fe9edc3ae318e980d28bbbd523dd523`
          FOREIGN KEY (`id_workflow_step`)
          REFERENCES `workflow_steps` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `campaigns`
          ADD CONSTRAINT `fk_2866af2b316947dd585fe33e58d08654`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `campaigns`
          ADD CONSTRAINT `fk_17d71972a02553b4ea7231c316268415`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `campaigns`
          ADD CONSTRAINT `fk_4d8d1a74204267b2c167af6e85fe4b6d`
          FOREIGN KEY (`id_launched_by`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `campaigns`
          DROP FOREIGN KEY `fk_a588511694e8c9d43e6afa9d3e84a036`; ALTER TABLE `campaigns`
          DROP FOREIGN KEY `fk_c27abb2d87accd8d73befc05de085a93`; ALTER TABLE `campaigns`
          DROP FOREIGN KEY `fk_56100642a935b1f9d63eaef207abb7ef`; ALTER TABLE `campaigns`
          DROP FOREIGN KEY `fk_0fe9edc3ae318e980d28bbbd523dd523`; ALTER TABLE `campaigns`
          DROP FOREIGN KEY `fk_2866af2b316947dd585fe33e58d08654`; ALTER TABLE `campaigns`
          DROP FOREIGN KEY `fk_17d71972a02553b4ea7231c316268415`; ALTER TABLE `campaigns`
          DROP FOREIGN KEY `fk_4d8d1a74204267b2c167af6e85fe4b6d`;");
  }
}