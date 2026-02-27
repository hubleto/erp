<?php

namespace Hubleto\App\Community\Issues\Models\Migrations;

use Hubleto\Framework\Migration;

class MailAccount_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `issues_mail_accounts`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `issues_mail_accounts` (
 `id` int(8) primary key auto_increment,
 `id_mail_account` int(8) NULL default NULL,
 `settings` text ,
 `notes` varchar(255) ,
 index `id` (`id`),
 index `id_mail_account` (`id_mail_account`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `issues_mail_accounts`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `issues_mail_accounts`
          ADD CONSTRAINT `fk_85b2115fa8e81ffc4a6861013a7f6c75`
          FOREIGN KEY (`id_mail_account`)
          REFERENCES `mails_accounts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `issues_mail_accounts`
          DROP FOREIGN KEY `fk_85b2115fa8e81ffc4a6861013a7f6c75`;");
  }
}