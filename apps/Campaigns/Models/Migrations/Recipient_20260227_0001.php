<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Recipient_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `campaigns_recipients`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `campaigns_recipients` (
 `id` int(8) primary key auto_increment,
 `id_campaign` int(8) NULL default NULL,
 `id_contact` int(8) NULL default NULL,
 `email` varchar(255) ,
 `first_name` varchar(255) ,
 `last_name` varchar(255) ,
 `salutation` varchar(255) ,
 `variables` text ,
 `id_mail` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_campaign` (`id_campaign`),
 index `id_contact` (`id_contact`),
 index `id_mail` (`id_mail`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `campaigns_recipients`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `campaigns_recipients`
          ADD CONSTRAINT `fk_881d25d7fd0050fc95f44c62c8377741`
          FOREIGN KEY (`id_campaign`)
          REFERENCES `campaigns` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `campaigns_recipients`
          ADD CONSTRAINT `fk_d2b02a0afe04a2682e6548d6fe86f623`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `campaigns_recipients`
          ADD CONSTRAINT `fk_3c5082c78a49926bd3af30334ce1894d`
          FOREIGN KEY (`id_mail`)
          REFERENCES `mails` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `campaigns_recipients`
          DROP FOREIGN KEY `fk_881d25d7fd0050fc95f44c62c8377741`; ALTER TABLE `campaigns_recipients`
          DROP FOREIGN KEY `fk_d2b02a0afe04a2682e6548d6fe86f623`; ALTER TABLE `campaigns_recipients`
          DROP FOREIGN KEY `fk_3c5082c78a49926bd3af30334ce1894d`;");
  }
}