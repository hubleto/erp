<?php

namespace Hubleto\App\Community\Invoices\Models\Migrations;

use Hubleto\Framework\Migration;

class Profile_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `invoice_profiles`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `invoice_profiles` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `headline` varchar(255) ,
 `id_company` int(8) NULL default NULL,
 `numbering_pattern` varchar(255) ,
 `invoice_type_prefixes` text ,
 `iban` varchar(255) ,
 `swift` varchar(255) ,
 `id_currency` int(8) NULL default NULL,
 `is_default` int(1) ,
 `due_days` int(255) ,
 `stamp_and_signature` varchar(255) ,
 `id_template` int(8) NULL default NULL,
 `id_sender_account` int(8) NULL default NULL,
 `id_payment_method` int(8) NULL default NULL,
 `mail_send_invoice_subject` varchar(255) ,
 `mail_send_invoice_body` text ,
 `mail_send_invoice_cc` varchar(255) ,
 `mail_send_invoice_bcc` varchar(255) ,
 `mail_send_due_warning_subject` varchar(255) ,
 `mail_send_due_warning_body` text ,
 `mail_send_due_warning_cc` varchar(255) ,
 `mail_send_due_warning_bcc` varchar(255) ,
 index `id` (`id`),
 index `id_company` (`id_company`),
 index `id_currency` (`id_currency`),
 index `is_default` (`is_default`),
 index `due_days` (`due_days`),
 index `id_template` (`id_template`),
 index `id_sender_account` (`id_sender_account`),
 index `id_payment_method` (`id_payment_method`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `invoice_profiles`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `invoice_profiles`
          ADD CONSTRAINT `fk_75c907b5b7dde507ca537437c715e7c8`
          FOREIGN KEY (`id_company`)
          REFERENCES `companies` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoice_profiles`
          ADD CONSTRAINT `fk_b40b275fc5a5fd0604e51b14e5dcf356`
          FOREIGN KEY (`id_currency`)
          REFERENCES `currencies` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoice_profiles`
          ADD CONSTRAINT `fk_c7ed84e290a6b504d9c74cfe6b43a8be`
          FOREIGN KEY (`id_template`)
          REFERENCES `documents_templates` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoice_profiles`
          ADD CONSTRAINT `fk_65d3818a07dc54b2611d072086a271a2`
          FOREIGN KEY (`id_sender_account`)
          REFERENCES `mails_accounts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoice_profiles`
          ADD CONSTRAINT `fk_cd54ed9724a8ad5b5873861ae41fdc27`
          FOREIGN KEY (`id_payment_method`)
          REFERENCES `invoice_payment_methods` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `invoice_profiles`
          DROP FOREIGN KEY `fk_75c907b5b7dde507ca537437c715e7c8`; ALTER TABLE `invoice_profiles`
          DROP FOREIGN KEY `fk_b40b275fc5a5fd0604e51b14e5dcf356`; ALTER TABLE `invoice_profiles`
          DROP FOREIGN KEY `fk_c7ed84e290a6b504d9c74cfe6b43a8be`; ALTER TABLE `invoice_profiles`
          DROP FOREIGN KEY `fk_65d3818a07dc54b2611d072086a271a2`; ALTER TABLE `invoice_profiles`
          DROP FOREIGN KEY `fk_cd54ed9724a8ad5b5873861ae41fdc27`;");
  }
}