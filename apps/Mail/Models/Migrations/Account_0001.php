<?php

namespace Hubleto\App\Community\Mail\Models\Migrations;

use Hubleto\Framework\Migration;

class Account_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `mails_accounts`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `mails_accounts` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `color` char(10) ,
 `id_owner` int(8) NULL default NULL,
 `id_manager` int(8) NULL default NULL,
 `sender_email` varchar(255) ,
 `sender_name` varchar(255) ,
 `imap_host` varchar(255) ,
 `imap_port` int(255) ,
 `imap_encryption` varchar(255) ,
 `imap_username` varchar(255) ,
 `imap_password` varchar(255) ,
 `smtp_host` varchar(255) ,
 `smtp_port` int(255) ,
 `smtp_encryption` varchar(255) ,
 `smtp_username` varchar(255) ,
 `smtp_password` varchar(255) ,
 index `id` (`id`),
 index `id_owner` (`id_owner`),
 index `id_manager` (`id_manager`),
 index `imap_port` (`imap_port`),
 index `smtp_port` (`smtp_port`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `mails_accounts`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `mails_accounts`
          ADD CONSTRAINT `fk_dd0447e959ea18ac9d4a0d894ad3723e`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `mails_accounts`
          ADD CONSTRAINT `fk_fca8eb0521c38c0bf7dfc677d50c5d38`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `mails_accounts`
          DROP FOREIGN KEY `fk_dd0447e959ea18ac9d4a0d894ad3723e`; ALTER TABLE `mails_accounts`
          DROP FOREIGN KEY `fk_fca8eb0521c38c0bf7dfc677d50c5d38`;");
  }
}