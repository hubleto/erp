<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class EmailClick_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_email_clicks`;
      create table `email_marketing_email_clicks` (
        `id` int(8) primary key auto_increment,
        `id_email` int(8) NULL default NULL,
        `id_recipient` int(8) NULL default NULL,
        `url` varchar(255),
        `datetime_clicked` datetime,
        `log` json,
        `bot_score` int(8),
        index `id` (`id`),
        index `id_email` (`id_email`),
        index `id_recipient` (`id_recipient`),
        index `datetime_clicked` (`datetime_clicked`),
        index `bot_score` (`bot_score`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_email_clicks`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_email_clicks` ADD CONSTRAINT `fk__email_marketing_email_clicks__id_email`
      FOREIGN KEY (`id_email`) REFERENCES `email_marketing_emails` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

      ALTER TABLE `email_marketing_email_clicks` ADD CONSTRAINT `fk__email_marketing_email_clicks__id_recipient`
      FOREIGN KEY (`id_recipient`) REFERENCES `email_marketing_email_recipients` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_email_clicks` DROP FOREIGN KEY `fk__email_marketing_email_clicks__id_email`;
      ALTER TABLE `email_marketing_email_clicks` DROP FOREIGN KEY `fk__email_marketing_email_clicks__id_recipient`;
    ");
  }
}