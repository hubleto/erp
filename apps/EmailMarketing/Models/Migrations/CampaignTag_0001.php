<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class CampaignTag_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_campaign_tags`;
      create table `email_marketing_campaign_tags` (
        `id` int(8) primary key auto_increment,
        `id_campaign` int(8) NULL default NULL,
        `id_tag` int(8) NULL default NULL,
        index `id` (`id`),
        index `id_campaign` (`id_campaign`),
        index `id_tag` (`id_tag`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_campaign_tags`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_campaign_tags` ADD CONSTRAINT `fk__email_marketing_campaign_tags__id_campaign`
      FOREIGN KEY (`id_campaign`) REFERENCES `email_marketing_campaigns` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `email_marketing_campaign_tags` ADD CONSTRAINT `fk__email_marketing_campaign_tags__id_tag`
      FOREIGN KEY (`id_tag`) REFERENCES `email_marketing_tags` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_campaign_tags`
      DROP FOREIGN KEY `fk__email_marketing_campaign_tags__id_campaign`;
      
      ALTER TABLE `email_marketing_campaign_tags`
      DROP FOREIGN KEY `fk__email_marketing_campaign_tags__id_tag`;
    ");
  }
}