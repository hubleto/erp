<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Recipient_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_recipients`;
      create table `email_marketing_recipients` (
        `id` int(8) primary key auto_increment,
        `id_email` int(8) NULL default NULL,
        `id_contact` int(8) NULL default NULL,
        `email` varchar(255),
        `first_name` varchar(255),
        `last_name` varchar(255),
        `salutation` varchar(255),
        `variables` text,
        `id_mail` int(8) NULL default NULL,
        `notes` text,
        index `id` (`id`),
        index `id_email` (`id_email`),
        index `id_contact` (`id_contact`),
        index `id_mail` (`id_mail`),
        index `email` (`email`),
        unique index `email_marketing_recipients__id_email__email` (`id_email`, `email`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_recipients`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_recipients` ADD CONSTRAINT `fk__email_marketing_recipients__id_email`
      FOREIGN KEY (`id_email`) REFERENCES `email_marketing_recipients` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

      ALTER TABLE `email_marketing_recipients` ADD CONSTRAINT `fk__email_marketing_recipients__id_contact`
      FOREIGN KEY (`id_contact`) REFERENCES `contacts` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;

      ALTER TABLE `email_marketing_recipients` ADD CONSTRAINT `fk__email_marketing_recipients__id_mail`
      FOREIGN KEY (`id_mail`) REFERENCES `mails` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_recipients` DROP FOREIGN KEY `fk__email_marketing_recipients__id_email`;
      ALTER TABLE `email_marketing_recipients` DROP FOREIGN KEY `fk__email_marketing_recipients__id_contact`;
      ALTER TABLE `email_marketing_recipients` DROP FOREIGN KEY `fk__email_marketing_recipients__id_mail`;
    ");
  }
}