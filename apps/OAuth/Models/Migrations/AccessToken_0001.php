<?php

namespace Hubleto\App\Community\OAuth\Models\Migrations;

use Hubleto\Framework\Migration;

class AccessToken_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `oauth_access_tokens`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `oauth_access_tokens` (
 `id` int(8) primary key auto_increment,
 `access_token` varchar(255) ,
 `expires_at` varchar(255) ,
 `client_id` varchar(255) ,
 `scopes` varchar(255) ,
 `revoked` int(1) ,
 index `id` (`id`),
 index `revoked` (`revoked`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `oauth_access_tokens`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    
  }

  public function downgradeForeignKeys(): void
  {
    
  }
}