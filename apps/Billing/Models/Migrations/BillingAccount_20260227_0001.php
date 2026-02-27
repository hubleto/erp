<?php

namespace Hubleto\App\Community\Billing\Models\Migrations;

use Hubleto\Framework\Migration;

class BillingAccount_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `billing_accounts`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `billing_accounts` (
 `id` int(8) primary key auto_increment,
 `id_customer` int(8) NULL default NULL,
 `description` varchar(255) ,
 index `id` (`id`),
 index `id_customer` (`id_customer`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `billing_accounts`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `billing_accounts`
          ADD CONSTRAINT `fk_118a033d607d5ea722d2066bdc36d63f`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `billing_accounts`
          DROP FOREIGN KEY `fk_118a033d607d5ea722d2066bdc36d63f`;");
  }
}