<?php

namespace Hubleto\App\Community\Customers\Models\Migrations;

use Hubleto\Framework\Migration;

class CustomerDocument_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `customer_documents`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
drop table if exists `customer_documents`;
create table `customer_documents` (
 `id` int(8) primary key auto_increment,
 `id_customer` int(8) NULL default NULL,
 `id_document` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_customer` (`id_customer`),
 index `id_document` (`id_document`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `customer_documents`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `customer_documents`
          ADD CONSTRAINT `fk_3ec3fee5e0f22f6622abf685ad257d84`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE CASCADE
          ON UPDATE CASCADE; ALTER TABLE `customer_documents`
          ADD CONSTRAINT `fk_2fe3769e49b59bc03fe03d64912a4776`
          FOREIGN KEY (`id_document`)
          REFERENCES `documents` (`id`)
          ON DELETE CASCADE
          ON UPDATE CASCADE;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `customer_documents`
          DROP FOREIGN KEY `fk_3ec3fee5e0f22f6622abf685ad257d84`; ALTER TABLE `customer_documents`
          DROP FOREIGN KEY `fk_2fe3769e49b59bc03fe03d64912a4776`;");
  }
}