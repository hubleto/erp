<?php

namespace Hubleto\App\Community\Customers\Models\Migrations;

use Hubleto\Framework\Migration;

class CustomerTag_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cross_customer_tags`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cross_customer_tags` (
 `id` int(8) primary key auto_increment,
 `id_customer` int(8) NULL default NULL,
 `id_tag` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_customer` (`id_customer`),
 index `id_tag` (`id_tag`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cross_customer_tags`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cross_customer_tags`
          ADD CONSTRAINT `fk_4534f7a1136b391bdbb126fd951fb16e`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `cross_customer_tags`
          ADD CONSTRAINT `fk_a9f071d7a0eb3e2feb77bdb337078c8b`
          FOREIGN KEY (`id_tag`)
          REFERENCES `customer_tags` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cross_customer_tags`
          DROP FOREIGN KEY `fk_4534f7a1136b391bdbb126fd951fb16e`; ALTER TABLE `cross_customer_tags`
          DROP FOREIGN KEY `fk_a9f071d7a0eb3e2feb77bdb337078c8b`;");
  }
}