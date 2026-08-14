<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class RecipientStatus_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `campaigns_recipient_statuses`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `campaigns_recipient_statuses` (
 `id` int(8) primary key auto_increment,
 `email` varchar(255) ,
 `is_opted_out` int(1) ,
 `is_invalid` int(1) ,
 index `id` (`id`),
 INDEX `email` (`email`),
 index `is_opted_out` (`is_opted_out`),
 index `is_invalid` (`is_invalid`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `campaigns_recipient_statuses`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}