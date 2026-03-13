<?php

namespace Hubleto\App\Community\Workflow\Models\Migrations;

use Hubleto\Framework\Migration;

class Workflow_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `workflows`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `workflows` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `order` int(255) ,
 `description` varchar(255) ,
 `show_in_kanban` int(1) ,
 `group` varchar(255) ,
 index `id` (`id`),
 index `order` (`order`),
 index `show_in_kanban` (`show_in_kanban`),
 INDEX `group` (`group`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `workflows`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}