<?php

namespace Hubleto\App\Community\Cloud\Models\Migrations;

use Hubleto\Framework\Migration;

class Payment_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cloud_payments`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cloud_payments` (
 `id` int(8) primary key auto_increment,
 `datetime_charged` datetime ,
 `discount_percent` decimal(14, 4) ,
 `full_amount` decimal(14, 2) ,
 `discounted_amount` decimal(14, 2) ,
 `type` int(255) ,
 `details` text ,
 `has_invoice` int(1) ,
 `id_billing_account` int(8) NULL default NULL,
 `uuid` varchar(255) ,
 index `id` (`id`),
 index `datetime_charged` (`datetime_charged`),
 index `type` (`type`),
 index `has_invoice` (`has_invoice`),
 index `id_billing_account` (`id_billing_account`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cloud_payments`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cloud_payments`
          ADD CONSTRAINT `fk_698acefe52e20e5648ed684508cbc10d`
          FOREIGN KEY (`id_billing_account`)
          REFERENCES `cloud_billing_accounts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `cloud_payments`
          DROP FOREIGN KEY `fk_698acefe52e20e5648ed684508cbc10d`;");
  }
}