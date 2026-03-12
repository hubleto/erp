<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class History_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `order_histories`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `order_histories` (
 `id` int(8) primary key auto_increment,
 `id_order` int(8) NULL default NULL,
 `short_description` varchar(255) ,
 `long_description` text ,
 `date_time` datetime ,
 index `id` (`id`),
 index `id_order` (`id_order`),
 index `date_time` (`date_time`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `order_histories`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `order_histories`
          ADD CONSTRAINT `fk_ca9d1891bb0f7d97e25e2509adc63f25`
          FOREIGN KEY (`id_order`)
          REFERENCES `orders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `order_histories`
          DROP FOREIGN KEY `fk_ca9d1891bb0f7d97e25e2509adc63f25`;");
  }
}