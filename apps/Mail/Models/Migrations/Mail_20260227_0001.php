<?php

namespace Hubleto\App\Community\Mail\Models\Migrations;

use Hubleto\Framework\Migration;

class Mail_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `mails`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `mails` (
 `id` int(8) primary key auto_increment,
 `mail_number` varchar(255) ,
 `mail_id` varchar(255) ,
 `id_account` int(8) NULL default NULL,
 `id_mailbox` int(8) NULL default NULL,
 `priority` int(255) ,
 `datetime_created` datetime ,
 `datetime_scheduled_to_send` datetime ,
 `datetime_sent` datetime ,
 `datetime_read` datetime ,
 `subject` varchar(255) ,
 `from` varchar(255) ,
 `to` varchar(255) ,
 `cc` varchar(255) ,
 `bcc` varchar(255) ,
 `reply_to` varchar(255) ,
 `body_text` text ,
 `body_html` text ,
 `color` char(10) ,
 `is_draft` int(1) ,
 `is_template` int(1) ,
 index `id` (`id`),
 INDEX `mail_number` (`mail_number`),
 INDEX `mail_id` (`mail_id`),
 index `id_account` (`id_account`),
 index `id_mailbox` (`id_mailbox`),
 index `priority` (`priority`),
 index `datetime_created` (`datetime_created`),
 index `datetime_scheduled_to_send` (`datetime_scheduled_to_send`),
 index `datetime_sent` (`datetime_sent`),
 index `datetime_read` (`datetime_read`),
 index `is_draft` (`is_draft`),
 index `is_template` (`is_template`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `mails`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `mails`
          ADD CONSTRAINT `fk_909d6fb1fae27db8a9246154ea1a773b`
          FOREIGN KEY (`id_account`)
          REFERENCES `mails_accounts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `mails`
          ADD CONSTRAINT `fk_0cb1e05258d325aa85e4971cd14a8244`
          FOREIGN KEY (`id_mailbox`)
          REFERENCES `mails_mailboxes` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `mails`
          DROP FOREIGN KEY `fk_909d6fb1fae27db8a9246154ea1a773b`; ALTER TABLE `mails`
          DROP FOREIGN KEY `fk_0cb1e05258d325aa85e4971cd14a8244`;");
  }
}