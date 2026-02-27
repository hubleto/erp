<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class ProjectOrder_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_orders`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `projects_orders` (
 `id` int(8) primary key auto_increment,
 `id_project` int(8) NULL default NULL,
 `id_order` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_project` (`id_project`),
 index `id_order` (`id_order`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_orders`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_orders`
          ADD CONSTRAINT `fk_182ea7efb954103c25c47d516b551605`
          FOREIGN KEY (`id_project`)
          REFERENCES `projects` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects_orders`
          ADD CONSTRAINT `fk_1160455f19e4139930e1cf8666134bcf`
          FOREIGN KEY (`id_order`)
          REFERENCES `orders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_orders`
          DROP FOREIGN KEY `fk_182ea7efb954103c25c47d516b551605`; ALTER TABLE `projects_orders`
          DROP FOREIGN KEY `fk_1160455f19e4139930e1cf8666134bcf`;");
  }
}