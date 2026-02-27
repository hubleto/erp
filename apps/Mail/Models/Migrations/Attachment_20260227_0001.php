<?php

namespace Hubleto\App\Community\Mail\Models\Migrations;

use Hubleto\Framework\Migration;

class Attachment_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `mails_attachments`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `mails_attachments` (
 `id` int(8) primary key auto_increment,
 `id_mail` int(8) NULL default NULL,
 `name` varchar(255) ,
 `file` varchar(255) ,
 index `id` (`id`),
 index `id_mail` (`id_mail`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `mails_attachments`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `mails_attachments`
          ADD CONSTRAINT `fk_6f5fe75bcac3c3f635c10526c0ef7371`
          FOREIGN KEY (`id_mail`)
          REFERENCES `mails` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `mails_attachments`
          DROP FOREIGN KEY `fk_6f5fe75bcac3c3f635c10526c0ef7371`;");
  }
}