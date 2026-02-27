<?php

namespace Hubleto\App\Community\Suppliers\Models\Migrations;

use Hubleto\Framework\Migration;

class Supplier_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `suppliers`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `suppliers` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `address` varchar(255) ,
 `city` varchar(255) ,
 `postal_code` varchar(255) ,
 `id_country` int(8) NULL default NULL,
 `id_contact` int(8) NULL default NULL,
 `order_email` varchar(255) ,
 `tax_id` varchar(255) ,
 `company_id` varchar(255) ,
 `vat_id` varchar(255) ,
 `payment_account` varchar(255) ,
 index `id` (`id`),
 index `id_country` (`id_country`),
 index `id_contact` (`id_contact`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `suppliers`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `suppliers`
          ADD CONSTRAINT `fk_3307050a5435fdc860feb7963f61e34b`
          FOREIGN KEY (`id_country`)
          REFERENCES `countries` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `suppliers`
          ADD CONSTRAINT `fk_7f2d6e2906159bcc50ea39a12332085c`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `suppliers`
          DROP FOREIGN KEY `fk_3307050a5435fdc860feb7963f61e34b`; ALTER TABLE `suppliers`
          DROP FOREIGN KEY `fk_7f2d6e2906159bcc50ea39a12332085c`;");
  }
}