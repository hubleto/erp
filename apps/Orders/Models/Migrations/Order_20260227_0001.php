<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Order_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `orders` (
 `id` int(8) primary key auto_increment,
 `purchase_sales` int(255) ,
 `identifier` varchar(255) ,
 `identifier_external` varchar(255) ,
 `title` varchar(255) ,
 `id_customer` int(8) NULL default NULL,
 `id_supplier` int(8) NULL default NULL,
 `id_owner` int(8) NULL default NULL,
 `id_manager` int(8) NULL default NULL,
 `shared_with` text ,
 `id_workflow` int(8) NULL default NULL,
 `id_workflow_step` int(8) NULL default NULL,
 `price_excl_vat` decimal(14, 4) ,
 `price_incl_vat` decimal(14, 4) ,
 `payment_period` int(255) ,
 `prepaid_working_hours` int(255) ,
 `prepaid_working_hours_period` int(255) ,
 `id_currency` int(8) NULL default NULL,
 `date_order` date ,
 `required_delivery_date` date ,
 `shipping_info` varchar(255) ,
 `note` text ,
 `shared_folder` varchar(255) ,
 `id_template` int(8) NULL default NULL,
 `pdf` varchar(255) ,
 `is_closed` int(1) ,
 index `id` (`id`),
 index `purchase_sales` (`purchase_sales`),
 index `id_customer` (`id_customer`),
 index `id_supplier` (`id_supplier`),
 index `id_owner` (`id_owner`),
 index `id_manager` (`id_manager`),
 index `id_workflow` (`id_workflow`),
 index `id_workflow_step` (`id_workflow_step`),
 index `payment_period` (`payment_period`),
 index `prepaid_working_hours` (`prepaid_working_hours`),
 index `prepaid_working_hours_period` (`prepaid_working_hours_period`),
 index `id_currency` (`id_currency`),
 index `date_order` (`date_order`),
 index `required_delivery_date` (`required_delivery_date`),
 index `id_template` (`id_template`),
 index `is_closed` (`is_closed`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders`
          ADD CONSTRAINT `fk_42f77258a6783ca603df837293df525f`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders`
          ADD CONSTRAINT `fk_748ba119b00742bc90e2b1d80bb8854e`
          FOREIGN KEY (`id_supplier`)
          REFERENCES `suppliers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders`
          ADD CONSTRAINT `fk_dbc135e50f2d5445f7fd4e84761dc878`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders`
          ADD CONSTRAINT `fk_82a4d8cc0d2507e4c17703ae75665725`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders`
          ADD CONSTRAINT `fk_8820fcebe7111e917409f00e5d8cf247`
          FOREIGN KEY (`id_workflow`)
          REFERENCES `workflows` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders`
          ADD CONSTRAINT `fk_b774aeb5dc69c38170f66d6add6df54f`
          FOREIGN KEY (`id_workflow_step`)
          REFERENCES `workflow_steps` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders`
          ADD CONSTRAINT `fk_3b40b5f7362ee2fbd21767bf36943a3b`
          FOREIGN KEY (`id_currency`)
          REFERENCES `currencies` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders`
          ADD CONSTRAINT `fk_2b654dc51bdf2145c60961046cadbb15`
          FOREIGN KEY (`id_template`)
          REFERENCES `documents_templates` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders`
          DROP FOREIGN KEY `fk_42f77258a6783ca603df837293df525f`; ALTER TABLE `orders`
          DROP FOREIGN KEY `fk_748ba119b00742bc90e2b1d80bb8854e`; ALTER TABLE `orders`
          DROP FOREIGN KEY `fk_dbc135e50f2d5445f7fd4e84761dc878`; ALTER TABLE `orders`
          DROP FOREIGN KEY `fk_82a4d8cc0d2507e4c17703ae75665725`; ALTER TABLE `orders`
          DROP FOREIGN KEY `fk_8820fcebe7111e917409f00e5d8cf247`; ALTER TABLE `orders`
          DROP FOREIGN KEY `fk_b774aeb5dc69c38170f66d6add6df54f`; ALTER TABLE `orders`
          DROP FOREIGN KEY `fk_3b40b5f7362ee2fbd21767bf36943a3b`; ALTER TABLE `orders`
          DROP FOREIGN KEY `fk_2b654dc51bdf2145c60961046cadbb15`;");
  }
}