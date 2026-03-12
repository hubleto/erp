<?php

namespace Hubleto\App\Community\Cloud\Models\Migrations;

use Hubleto\Framework\Migration;

class BillingAccount_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cloud_billing_accounts`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `cloud_billing_accounts` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `phone` varchar(255) ,
 `email` varchar(255) ,
 `tax_id` varchar(255) ,
 `vat_id` varchar(255) ,
 `street_1` varchar(255) ,
 `street_2` varchar(255) ,
 `zip` varchar(255) ,
 `city` varchar(255) ,
 `country` varchar(255) ,
 `is_active` int(1) ,
 `datetime_created` datetime ,
 index `id` (`id`),
 index `is_active` (`is_active`),
 index `datetime_created` (`datetime_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `cloud_billing_accounts`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}