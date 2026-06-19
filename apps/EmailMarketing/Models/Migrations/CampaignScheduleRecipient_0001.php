<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class CampaignScheduleRecipient_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_campaigns_schedule_recipients`;
      create table `email_marketing_campaigns_schedule_recipients` (
        `id` int(8) primary key auto_increment,
        `id_campaign_schedule` int(8) NULL default NULL,
        `id_recipient` int(8) NULL default NULL,
        `id_mail` int(8) NULL default NULL,
        index `id` (`id`),
        index `id_campaign_schedule` (`id_campaign_schedule`),
        index `id_recipient` (`id_recipient`),
        index `id_mail` (`id_mail`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_campaigns_schedule_recipients`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("

      ALTER TABLE `email_marketing_campaigns_schedule_recipients` ADD CONSTRAINT `fk__email_marketing_campaigns_schedule_recipients__1`
      FOREIGN KEY (`id_campaign_schedule`) REFERENCES `email_marketing_campaigns_schedule` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

      ALTER TABLE `email_marketing_campaigns_schedule_recipients` ADD CONSTRAINT `fk__email_marketing_campaigns_schedule_recipients__id_recipient`
      FOREIGN KEY (`id_recipient`) REFERENCES `email_marketing_recipients` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

      ALTER TABLE `email_marketing_campaigns_schedule_recipients` ADD CONSTRAINT `fk__email_marketing_campaigns_schedule_recipients__id_mail`
      FOREIGN KEY (`id_mail`) REFERENCES `mails` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_campaigns_schedule_recipients` DROP FOREIGN KEY `fk__email_marketing_campaigns_schedule_recipients__1`;
      ALTER TABLE `email_marketing_campaigns_schedule_recipients` DROP FOREIGN KEY `fk__email_marketing_campaigns_schedule_recipients__id_recipient`;
      ALTER TABLE `email_marketing_campaigns_schedule_recipients` DROP FOREIGN KEY `fk__email_marketing_campaigns_schedule_recipients__id_mail`;
    ");
  }
}