<?php

namespace Hubleto\App\Community\Invoices\Models\Migrations;

use Hubleto\Framework\Migration;

class Invoice_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `invoices`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `invoices` (
 `id` int(8) primary key auto_increment,
 `inbound_outbound` int(255) ,
 `id_profile` int(8) NULL default NULL,
 `id_issued_by` int(8) NULL default NULL,
 `id_payment_method` int(8) NULL default NULL,
 `id_customer` int(8) NULL default NULL,
 `id_supplier` int(8) NULL default NULL,
 `type` int(255) ,
 `number` varchar(255) ,
 `number_external` varchar(255) ,
 `description` varchar(255) ,
 `vs` varchar(255) ,
 `cs` varchar(255) ,
 `ss` varchar(255) ,
 `date_issue` date ,
 `date_delivery` date ,
 `date_due` date ,
 `date_payment` date ,
 `date_sent` date ,
 `id_currency` int(8) NULL default NULL,
 `total_excl_vat` decimal(14, 2) ,
 `total_incl_vat` decimal(14, 2) ,
 `total_payments` decimal(14, 2) ,
 `notes` text ,
 `id_workflow` int(8) NULL default NULL,
 `id_workflow_step` int(8) NULL default NULL,
 `id_template` int(8) NULL default NULL,
 `pdf` varchar(255) ,
 index `id` (`id`),
 index `inbound_outbound` (`inbound_outbound`),
 index `id_profile` (`id_profile`),
 index `id_issued_by` (`id_issued_by`),
 index `id_payment_method` (`id_payment_method`),
 index `id_customer` (`id_customer`),
 index `id_supplier` (`id_supplier`),
 index `type` (`type`),
 index `date_issue` (`date_issue`),
 index `date_delivery` (`date_delivery`),
 index `date_due` (`date_due`),
 index `date_payment` (`date_payment`),
 index `date_sent` (`date_sent`),
 index `id_currency` (`id_currency`),
 index `id_workflow` (`id_workflow`),
 index `id_workflow_step` (`id_workflow_step`),
 index `id_template` (`id_template`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `invoices`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `invoices`
          ADD CONSTRAINT `fk_be6ae292e7186e49043272356ac4253b`
          FOREIGN KEY (`id_profile`)
          REFERENCES `invoice_profiles` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoices`
          ADD CONSTRAINT `fk_44c5b98aba93dea743786e095817b725`
          FOREIGN KEY (`id_issued_by`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoices`
          ADD CONSTRAINT `fk_f112e5ada9de00472b7eb8307787ff02`
          FOREIGN KEY (`id_payment_method`)
          REFERENCES `invoice_payment_methods` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoices`
          ADD CONSTRAINT `fk_ae1981f4e130a9375237e43653d7af41`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoices`
          ADD CONSTRAINT `fk_034919ca86f019145d52784cd716d2b8`
          FOREIGN KEY (`id_supplier`)
          REFERENCES `suppliers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoices`
          ADD CONSTRAINT `fk_6a785e3a15707002c6389ad6942e8b8e`
          FOREIGN KEY (`id_currency`)
          REFERENCES `currencies` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoices`
          ADD CONSTRAINT `fk_f70d742a01d0899f9d0a6bb67dea6776`
          FOREIGN KEY (`id_workflow`)
          REFERENCES `workflows` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoices`
          ADD CONSTRAINT `fk_a54f74d1e5b724964a891af0c38580ee`
          FOREIGN KEY (`id_workflow_step`)
          REFERENCES `workflow_steps` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `invoices`
          ADD CONSTRAINT `fk_9902b5f0a8be59218151a2e9eb18fff6`
          FOREIGN KEY (`id_template`)
          REFERENCES `documents_templates` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `invoices`
          DROP FOREIGN KEY `fk_be6ae292e7186e49043272356ac4253b`; ALTER TABLE `invoices`
          DROP FOREIGN KEY `fk_44c5b98aba93dea743786e095817b725`; ALTER TABLE `invoices`
          DROP FOREIGN KEY `fk_f112e5ada9de00472b7eb8307787ff02`; ALTER TABLE `invoices`
          DROP FOREIGN KEY `fk_ae1981f4e130a9375237e43653d7af41`; ALTER TABLE `invoices`
          DROP FOREIGN KEY `fk_034919ca86f019145d52784cd716d2b8`; ALTER TABLE `invoices`
          DROP FOREIGN KEY `fk_6a785e3a15707002c6389ad6942e8b8e`; ALTER TABLE `invoices`
          DROP FOREIGN KEY `fk_f70d742a01d0899f9d0a6bb67dea6776`; ALTER TABLE `invoices`
          DROP FOREIGN KEY `fk_a54f74d1e5b724964a891af0c38580ee`; ALTER TABLE `invoices`
          DROP FOREIGN KEY `fk_9902b5f0a8be59218151a2e9eb18fff6`;");
  }
}