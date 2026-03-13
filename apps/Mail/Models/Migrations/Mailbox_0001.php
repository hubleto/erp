<?php

namespace Hubleto\App\Community\Mail\Models\Migrations;

use Hubleto\Framework\Migration;

class Mailbox_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `mails_mailboxes`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `mails_mailboxes` (
 `id` int(8) primary key auto_increment,
 `id_account` int(8) NULL default NULL,
 `name` varchar(255) ,
 `attributes` varchar(255) ,
 index `id` (`id`),
 index `id_account` (`id_account`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `mails_mailboxes`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `mails_mailboxes`
          ADD CONSTRAINT `fk_087def3f9c240f3f7294517b166be1c7`
          FOREIGN KEY (`id_account`)
          REFERENCES `mails_accounts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `mails_mailboxes`
          DROP FOREIGN KEY `fk_087def3f9c240f3f7294517b166be1c7`;");
  }
}