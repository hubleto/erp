<?php

namespace Hubleto\App\Community\Auth\Models\Migrations;

use Hubleto\Framework\Migration;

class UserHasRole_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `user_has_roles`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `user_has_roles` (
 `id` int(8) primary key auto_increment,
 `id_user` int(8) NULL default NULL,
 `id_role` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_user` (`id_user`),
 index `id_role` (`id_role`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `user_has_roles`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `user_has_roles`
          ADD CONSTRAINT `fk_c6b196ebe80e0ca8aaff213129ba6532`
          FOREIGN KEY (`id_user`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `user_has_roles`
          ADD CONSTRAINT `fk_c51693c859a18914d83b3e176a8a692e`
          FOREIGN KEY (`id_role`)
          REFERENCES `user_roles` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `user_has_roles`
          DROP FOREIGN KEY `fk_c6b196ebe80e0ca8aaff213129ba6532`; ALTER TABLE `user_has_roles`
          DROP FOREIGN KEY `fk_c51693c859a18914d83b3e176a8a692e`;");
  }
}