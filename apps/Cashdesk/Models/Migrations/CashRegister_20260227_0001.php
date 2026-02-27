<?php

namespace Hubleto\App\Community\Cashdesk\Models\Migrations;

use Hubleto\Framework\Migration;

class CashRegister_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cashdesk_cash_registers`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cashdesk_cash_registers` (
 `id_company` int(8) NULL default NULL,
 `id_shop` int(8) NULL default NULL,
 `identifier` varchar(255) ,
 `description` varchar(255) ,
 `id` int(8) primary key auto_increment,
 index `id_company` (`id_company`),
 index `id_shop` (`id_shop`),
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cashdesk_cash_registers`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cashdesk_cash_registers`
          ADD CONSTRAINT `fk_15d70fdba9b4541acc9c83956b4284cc`
          FOREIGN KEY (`id_company`)
          REFERENCES `companies` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `cashdesk_cash_registers`
          ADD CONSTRAINT `fk_b9bd33d303c13720c9544481eeb789ec`
          FOREIGN KEY (`id_shop`)
          REFERENCES `shops` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cashdesk_cash_registers`
          DROP FOREIGN KEY `fk_15d70fdba9b4541acc9c83956b4284cc`; ALTER TABLE `cashdesk_cash_registers`
          DROP FOREIGN KEY `fk_b9bd33d303c13720c9544481eeb789ec`;");
  }
}