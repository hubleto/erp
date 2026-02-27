<?php

namespace Hubleto\App\Community\Cashdesk\Models\Migrations;

use Hubleto\Framework\Migration;

class Receipt_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cashdesk_receipts`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cashdesk_receipts` (
 `number` varchar(255) ,
 `id_company` int(8) NULL default NULL,
 `id_cash_register` int(8) NULL default NULL,
 `total_price_excl_vat` decimal(14, 4) ,
 `total_price_incl_vat` decimal(14, 4) ,
 `created` datetime ,
 `sent_to_cash_register` datetime ,
 `id` int(8) primary key auto_increment,
 index `id_company` (`id_company`),
 index `id_cash_register` (`id_cash_register`),
 index `created` (`created`),
 index `sent_to_cash_register` (`sent_to_cash_register`),
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cashdesk_receipts`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cashdesk_receipts`
          ADD CONSTRAINT `fk_3d752d7ede56d7b7561e01d7fc7ed02f`
          FOREIGN KEY (`id_company`)
          REFERENCES `companies` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `cashdesk_receipts`
          ADD CONSTRAINT `fk_eb108005ef25bbd59b78b7cdade91385`
          FOREIGN KEY (`id_cash_register`)
          REFERENCES `cashdesk_cash_registers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cashdesk_receipts`
          DROP FOREIGN KEY `fk_3d752d7ede56d7b7561e01d7fc7ed02f`; ALTER TABLE `cashdesk_receipts`
          DROP FOREIGN KEY `fk_eb108005ef25bbd59b78b7cdade91385`;");
  }
}