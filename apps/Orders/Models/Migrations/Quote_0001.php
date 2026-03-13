<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Quote_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders_quotes`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `orders_quotes` (
 `id` int(8) primary key auto_increment,
 `id_order` int(8) NULL default NULL,
 `version` int(255) ,
 `date_created` date ,
 `date_sent` date ,
 `id_approved_by` int(8) NULL default NULL,
 `date_approved` date ,
 `date_accepted` date ,
 `summary` text ,
 `online_document_1` varchar(255) ,
 `online_document_2` varchar(255) ,
 `online_document_3` varchar(255) ,
 `online_document_4` varchar(255) ,
 `online_document_5` varchar(255) ,
 `final_pdf_1` varchar(255) ,
 `final_pdf_2` varchar(255) ,
 `final_pdf_3` varchar(255) ,
 `final_pdf_4` varchar(255) ,
 `final_pdf_5` varchar(255) ,
 `notes_document_1` text ,
 `notes_document_2` text ,
 `notes_document_3` text ,
 `notes_document_4` text ,
 `notes_document_5` text ,
 `id_owner` int(8) NULL default NULL,
 `id_manager` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_order` (`id_order`),
 index `version` (`version`),
 index `date_created` (`date_created`),
 index `date_sent` (`date_sent`),
 index `id_approved_by` (`id_approved_by`),
 index `date_approved` (`date_approved`),
 index `date_accepted` (`date_accepted`),
 index `id_owner` (`id_owner`),
 index `id_manager` (`id_manager`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders_quotes`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders_quotes`
          ADD CONSTRAINT `fk_f65c313bdd838ae7aeebee28828c31e9`
          FOREIGN KEY (`id_order`)
          REFERENCES `orders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_quotes`
          ADD CONSTRAINT `fk_08c1abc77a4d19a6346d644a747dc044`
          FOREIGN KEY (`id_approved_by`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_quotes`
          ADD CONSTRAINT `fk_0581be6587c4fb5ad8a7692d30465c48`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_quotes`
          ADD CONSTRAINT `fk_1a8b2caf744312e5086e967399f22f9d`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders_quotes`
          DROP FOREIGN KEY `fk_f65c313bdd838ae7aeebee28828c31e9`; ALTER TABLE `orders_quotes`
          DROP FOREIGN KEY `fk_08c1abc77a4d19a6346d644a747dc044`; ALTER TABLE `orders_quotes`
          DROP FOREIGN KEY `fk_0581be6587c4fb5ad8a7692d30465c48`; ALTER TABLE `orders_quotes`
          DROP FOREIGN KEY `fk_1a8b2caf744312e5086e967399f22f9d`;");
  }
}