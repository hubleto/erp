<?php

namespace Hubleto\App\Community\Cloud\Models\Migrations;

use Hubleto\Framework\Migration;

class Discount_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cloud_discounts`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cloud_discounts` (
 `id` int(8) primary key auto_increment,
 `year` int(255) ,
 `month` int(255) ,
 `discount_percent` int(255) ,
 `notes` varchar(255) ,
 `id_billing_account` int(8) NULL default NULL,
 index `id` (`id`),
 index `year` (`year`),
 index `month` (`month`),
 index `discount_percent` (`discount_percent`),
 index `id_billing_account` (`id_billing_account`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cloud_discounts`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cloud_discounts`
          ADD CONSTRAINT `fk_4964c35ff92b1fde2d88c4e5b7450e6e`
          FOREIGN KEY (`id_billing_account`)
          REFERENCES `cloud_billing_accounts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cloud_discounts`
          DROP FOREIGN KEY `fk_4964c35ff92b1fde2d88c4e5b7450e6e`;");
  }
}