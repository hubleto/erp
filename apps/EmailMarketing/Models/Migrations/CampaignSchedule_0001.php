<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class CampaignSchedule_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_campaigns_schedule`;
      create table `email_marketing_campaigns_schedule` (
        `id` int(8) primary key auto_increment,
        `id_campaign` int(8) NULL default NULL,
        `day` int(8),
        `id_email` int(8) NULL default NULL,
        index `id` (`id`),
        index `id_email` (`id_email`),
        index `id_campaign` (`id_campaign`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_campaigns_schedule`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_campaigns_schedule` ADD CONSTRAINT `fk__email_marketing_campaigns_schedule__id_email`
      FOREIGN KEY (`id_email`) REFERENCES `email_marketing_emails` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

      ALTER TABLE `email_marketing_campaigns_schedule` ADD CONSTRAINT `fk__email_marketing_campaigns_schedule__id_campaign`
      FOREIGN KEY (`id_campaign`) REFERENCES `email_marketing_campaigns` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_campaigns_schedule` DROP FOREIGN KEY `fk__email_marketing_campaigns_schedule__id_email`;
      ALTER TABLE `email_marketing_campaigns_schedule` DROP FOREIGN KEY `fk__email_marketing_campaigns_schedule__id_campaign`;
    ");
  }
}