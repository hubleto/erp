<?php

namespace Hubleto\App\Community\Customers\Models\Migrations;

use http\Exception\BadMethodCallException;
use Hubleto\Framework\Migration;

class Tag_26_02_2026_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("SET foreign_key_checks = 0;
drop table if exists `customer_tags`;
create table `customer_tags` (
 `id` int(8) primary key auto_increment,
 `name` varchar(255) ,
 `color` char(10) ,
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS customer_tags;");
  }

  public function installForeignKeys(): void
  {
    
  }

  public function uninstallForeignKeys(): void
  {
    
  }
}