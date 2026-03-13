<?php

namespace Hubleto\App\Community\OAuth\Models\Migrations;

use Hubleto\Framework\Migration;

class Client_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `oauth_clients`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `oauth_clients` (
 `id` int(8) primary key auto_increment,
 `client_id` varchar(255) ,
 `client_secret` varchar(255) ,
 `name` varchar(255) ,
 `redirect_uri` varchar(255) ,
 index `id` (`id`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `oauth_clients`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}