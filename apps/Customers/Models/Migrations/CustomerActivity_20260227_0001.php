<?php

namespace Hubleto\App\Community\Customers\Models\Migrations;

use Hubleto\Framework\Migration;

class CustomerActivity_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `customer_activities`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `customer_activities` (
 `id` int(8) primary key auto_increment,
 `subject` varchar(255) ,
 `location` varchar(255) ,
 `online_meeting_link` varchar(255) ,
 `description` text ,
 `id_activity_type` int(8) NULL default NULL,
 `date_start` date ,
 `time_start` time ,
 `date_end` date ,
 `time_end` time ,
 `recurrence` text ,
 `all_day` int(1) ,
 `completed` int(1) ,
 `meeting_minutes_link` varchar(255) ,
 `meeting_minutes` text ,
 `id_owner` int(8) NULL default NULL,
 `id_customer` int(8) NULL default NULL,
 `id_contact` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_activity_type` (`id_activity_type`),
 index `date_start` (`date_start`),
 index `time_start` (`time_start`),
 index `date_end` (`date_end`),
 index `time_end` (`time_end`),
 index `all_day` (`all_day`),
 index `completed` (`completed`),
 index `id_owner` (`id_owner`),
 index `id_customer` (`id_customer`),
 index `id_contact` (`id_contact`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `customer_activities`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `customer_activities`
          ADD CONSTRAINT `fk_94c6d20fea04cdeb5ffd8ccf087cfabc`
          FOREIGN KEY (`id_activity_type`)
          REFERENCES `activity_types` (`id`)
          ON DELETE SET NULL
          ON UPDATE SET NULL; ALTER TABLE `customer_activities`
          ADD CONSTRAINT `fk_f417800a58b890bf8de0538414771f7a`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `customer_activities`
          ADD CONSTRAINT `fk_b41ec889a837718a526733b1cf479d5d`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `customer_activities`
          ADD CONSTRAINT `fk_7d4fe8d158d42c58cbd7c044dca8a84e`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `customer_activities`
          DROP FOREIGN KEY `fk_94c6d20fea04cdeb5ffd8ccf087cfabc`; ALTER TABLE `customer_activities`
          DROP FOREIGN KEY `fk_f417800a58b890bf8de0538414771f7a`; ALTER TABLE `customer_activities`
          DROP FOREIGN KEY `fk_b41ec889a837718a526733b1cf479d5d`; ALTER TABLE `customer_activities`
          DROP FOREIGN KEY `fk_7d4fe8d158d42c58cbd7c044dca8a84e`;");
  }
}