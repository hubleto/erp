<?php

namespace Hubleto\App\Community\Invoices\Models\Migrations;

use Hubleto\Framework\Migration;

class Payment_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `invoice_payments`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `invoice_payments` (
 `id` int(8) primary key auto_increment,
 `id_invoice` int(8) NULL default NULL,
 `date_payment` date ,
 `amount` decimal(14, 4) ,
 `is_advance_payment` int(1) ,
 index `id` (`id`),
 index `id_invoice` (`id_invoice`),
 index `date_payment` (`date_payment`),
 index `is_advance_payment` (`is_advance_payment`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `invoice_payments`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `invoice_payments`
          ADD CONSTRAINT `fk_c2f5d21a14b13ebad4e20f03a6ad12e6`
          FOREIGN KEY (`id_invoice`)
          REFERENCES `invoices` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `invoice_payments`
          DROP FOREIGN KEY `fk_c2f5d21a14b13ebad4e20f03a6ad12e6`;");
  }
}