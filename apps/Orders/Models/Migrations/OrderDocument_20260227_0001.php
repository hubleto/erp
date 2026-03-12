<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class OrderDocument_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders_documents`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `orders_documents` (
 `id` int(8) primary key auto_increment,
 `id_order` int(8) NULL default NULL,
 `id_document` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_order` (`id_order`),
 index `id_document` (`id_document`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders_documents`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders_documents`
          ADD CONSTRAINT `fk_f2673c9fdc8e8934b3cef5a0ad068869`
          FOREIGN KEY (`id_order`)
          REFERENCES `orders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_documents`
          ADD CONSTRAINT `fk_c9010cd9a88b2fdb960e88fcc239aacf`
          FOREIGN KEY (`id_document`)
          REFERENCES `documents` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders_documents`
          DROP FOREIGN KEY `fk_f2673c9fdc8e8934b3cef5a0ad068869`; ALTER TABLE `orders_documents`
          DROP FOREIGN KEY `fk_c9010cd9a88b2fdb960e88fcc239aacf`;");
  }
}