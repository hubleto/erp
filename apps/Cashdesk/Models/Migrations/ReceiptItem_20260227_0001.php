<?php

namespace Hubleto\App\Community\Cashdesk\Models\Migrations;

use Hubleto\Framework\Migration;

class ReceiptItem_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cashdesk_receipts_items`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cashdesk_receipts_items` (
 `id` int(8) primary key auto_increment,
 `id_receipt` int(8) NULL default NULL,
 `id_product` int(8) NULL default NULL,
 `vat_percent` decimal(14, 4) ,
 `unit_price_excl_vat` decimal(14, 4) ,
 `unit_vat` decimal(14, 4) ,
 `unit_price_incl_vat` decimal(14, 4) ,
 `quantity` decimal(14, 4) ,
 `total_price_excl_vat` decimal(14, 4) ,
 `total_vat` decimal(14, 4) ,
 `total_price_incl_vat` decimal(14, 4) ,
 index `id` (`id`),
 index `id_receipt` (`id_receipt`),
 index `id_product` (`id_product`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cashdesk_receipts_items`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cashdesk_receipts_items`
          ADD CONSTRAINT `fk_c73332f4b678fa77219f6264402beb1e`
          FOREIGN KEY (`id_receipt`)
          REFERENCES `cashdesk_receipts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `cashdesk_receipts_items`
          ADD CONSTRAINT `fk_2b059fc46611ba55cb35be6d11ad2e74`
          FOREIGN KEY (`id_product`)
          REFERENCES `products` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cashdesk_receipts_items`
          DROP FOREIGN KEY `fk_c73332f4b678fa77219f6264402beb1e`; ALTER TABLE `cashdesk_receipts_items`
          DROP FOREIGN KEY `fk_2b059fc46611ba55cb35be6d11ad2e74`;");
  }
}