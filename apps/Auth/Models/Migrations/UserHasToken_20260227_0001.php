<?php

namespace Hubleto\App\Community\Auth\Models\Migrations;

use Hubleto\Framework\Migration;

class UserHasToken_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `user_has_tokens`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `user_has_tokens` (
 `id` int(8) primary key auto_increment,
 `id_user` int(8) NULL default NULL,
 `id_token` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_user` (`id_user`),
 index `id_token` (`id_token`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `user_has_tokens`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `user_has_tokens`
          ADD CONSTRAINT `fk_e050a7c1a961da8729c1ce5063a52b58`
          FOREIGN KEY (`id_user`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `user_has_tokens`
          ADD CONSTRAINT `fk_0bfc034bf664af960f7c6166c271c24d`
          FOREIGN KEY (`id_token`)
          REFERENCES `tokens` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `user_has_tokens`
          DROP FOREIGN KEY `fk_e050a7c1a961da8729c1ce5063a52b58`; ALTER TABLE `user_has_tokens`
          DROP FOREIGN KEY `fk_0bfc034bf664af960f7c6166c271c24d`;");
  }
}