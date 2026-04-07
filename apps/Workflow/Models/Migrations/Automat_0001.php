<?php

namespace Hubleto\App\Community\Workflow\Models\Migrations;

use Hubleto\Framework\Migration;

class Automat_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `workflow_automats`;
      create table `workflow_automats` (
        `id` int(8) primary key auto_increment,
        `name` varchar(255),
        `trigger` varchar(255),
        `execution_order` int(255),
        `description` varchar(255),
        `conditions` json,
        `actions` json,
        index `id` (`id`),
        index `trigger` (`trigger`),
        index `execution_order` (`execution_order`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `workflow_automats`;
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