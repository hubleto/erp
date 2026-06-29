<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Tag_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_tags`;
      create table `email_marketing_tags` (
        `id` int(8) primary key auto_increment,
        `name` varchar(255) ,
        `color` char(10) ,
        index `id` (`id`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `email_marketing_tags`;
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