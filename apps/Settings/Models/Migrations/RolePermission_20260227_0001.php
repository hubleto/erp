<?php

namespace Hubleto\App\Community\Settings\Models\Migrations;

use Hubleto\Framework\Migration;

class RolePermission_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `role_permissions`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `role_permissions` (
 `id` int(8) primary key auto_increment,
 `id_permission` int(8) NULL default NULL,
 `id_role` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_permission` (`id_permission`),
 index `id_role` (`id_role`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `role_permissions`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `role_permissions`
          ADD CONSTRAINT `fk_71ddfb9386651bad02927ce612ccc082`
          FOREIGN KEY (`id_permission`)
          REFERENCES `permissions` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `role_permissions`
          ADD CONSTRAINT `fk_ebe63d12f9405c8de64267bdee95c11b`
          FOREIGN KEY (`id_role`)
          REFERENCES `user_roles` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `role_permissions`
          DROP FOREIGN KEY `fk_71ddfb9386651bad02927ce612ccc082`; ALTER TABLE `role_permissions`
          DROP FOREIGN KEY `fk_ebe63d12f9405c8de64267bdee95c11b`;");
  }
}