<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class OrderDeal_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders_deals`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `orders_deals` (
 `id` int(8) primary key auto_increment,
 `id_order` int(8) NULL default NULL,
 `id_deal` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_order` (`id_order`),
 index `id_deal` (`id_deal`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `orders_deals`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders_deals`
          ADD CONSTRAINT `fk_9b80ba84cd7cfb35c94b1220f0c948d3`
          FOREIGN KEY (`id_order`)
          REFERENCES `orders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `orders_deals`
          ADD CONSTRAINT `fk_a7aa9733f7b918daddf2b8888c7f2e7d`
          FOREIGN KEY (`id_deal`)
          REFERENCES `deals` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders_deals`
          DROP FOREIGN KEY `fk_9b80ba84cd7cfb35c94b1220f0c948d3`; ALTER TABLE `orders_deals`
          DROP FOREIGN KEY `fk_a7aa9733f7b918daddf2b8888c7f2e7d`;");
  }
}