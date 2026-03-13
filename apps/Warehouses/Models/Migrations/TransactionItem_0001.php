<?php

namespace Hubleto\App\Community\Warehouses\Models\Migrations;

use Hubleto\Framework\Migration;

class TransactionItem_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses_transactions_items`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `warehouses_transactions_items` (
 `id` int(8) primary key auto_increment,
 `id_transaction` int(8) NULL default NULL,
 `id_product` int(8) NULL default NULL,
 `purchase_price` decimal(14, 4) ,
 `quantity` decimal(14, 4) ,
 index `id` (`id`),
 index `id_transaction` (`id_transaction`),
 index `id_product` (`id_product`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `warehouses_transactions_items`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `warehouses_transactions_items`
          ADD CONSTRAINT `fk_6854fa19ec737f79cd4607f5f0167ceb`
          FOREIGN KEY (`id_transaction`)
          REFERENCES `warehouses_transactions` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `warehouses_transactions_items`
          ADD CONSTRAINT `fk_7ca248b747fc139260b24bf76fe0e6be`
          FOREIGN KEY (`id_product`)
          REFERENCES `products` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `warehouses_transactions_items`
          DROP FOREIGN KEY `fk_6854fa19ec737f79cd4607f5f0167ceb`; ALTER TABLE `warehouses_transactions_items`
          DROP FOREIGN KEY `fk_7ca248b747fc139260b24bf76fe0e6be`;");
  }
}