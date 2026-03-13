<?php

namespace Hubleto\App\Community\Customers\Models\Migrations;

use Hubleto\Framework\Migration;

class Customer_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `customers`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `customers` (
 `name` varchar(255) ,
 `identifier` varchar(255) ,
 `street_line_1` varchar(255) ,
 `street_line_2` varchar(255) ,
 `region` varchar(255) ,
 `city` varchar(255) ,
 `postal_code` varchar(255) ,
 `id_country` int(8) NULL default NULL,
 `vat_id` varchar(255) ,
 `customer_id` varchar(255) ,
 `tax_id` varchar(255) ,
 `note` text ,
 `date_created` date ,
 `is_active` int(1) ,
 `id_owner` int(8) NULL default NULL,
 `id_manager` int(8) NULL default NULL,
 `shared_with` text ,
 `shared_folder` varchar(255) ,
 `id` int(8) primary key auto_increment,
 index `id_country` (`id_country`),
 index `date_created` (`date_created`),
 index `is_active` (`is_active`),
 index `id_owner` (`id_owner`),
 index `id_manager` (`id_manager`),
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;


              alter table `customers`
              add constraint `name` unique (`name` asc)
            ; 
              alter table `customers`
              add constraint `customer_id` unique (`customer_id` asc)
            ;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `customers`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `customers`
          ADD CONSTRAINT `fk_b87bcda2c4e5e71f81129209012a17fd`
          FOREIGN KEY (`id_country`)
          REFERENCES `countries` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `customers`
          ADD CONSTRAINT `fk_e24628ba0387820df0cbf2bc0ce07cf7`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `customers`
          ADD CONSTRAINT `fk_e02b3d35756c39f5ec3da79b8480f982`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `customers`
          DROP FOREIGN KEY `fk_b87bcda2c4e5e71f81129209012a17fd`; ALTER TABLE `customers`
          DROP FOREIGN KEY `fk_e24628ba0387820df0cbf2bc0ce07cf7`; ALTER TABLE `customers`
          DROP FOREIGN KEY `fk_e02b3d35756c39f5ec3da79b8480f982`;");
  }
}