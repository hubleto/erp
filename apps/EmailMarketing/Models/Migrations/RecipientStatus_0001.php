<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class RecipientStatus_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_recipient_statuses`;
      create table `email_marketing_recipient_statuses` (
        `id` int(8) primary key auto_increment,
        `email` varchar(255) ,
        `is_unsubscribed` int(1) ,
        `is_invalid` int(1) ,
        index `id` (`id`),
        INDEX `email` (`email`),
        index `is_unsubscribed` (`is_unsubscribed`),
        index `is_invalid` (`is_invalid`),
        unique index `email_unique` (`email`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_recipient_statuses`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}