<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class Folder_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `folders`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `folders` (
 `id` int(8) primary key auto_increment,
 `uid` varchar(255) ,
 `id_parent_folder` int(8) NULL default NULL,
 `name` varchar(255) ,
 index `id` (`id`),
 index `id_parent_folder` (`id_parent_folder`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;


              alter table `folders`
              add constraint `uid` unique (`uid` asc)
            ;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `folders`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `folders`
          ADD CONSTRAINT `fk_9eef5b52fd6d0a1680220c056355837e`
          FOREIGN KEY (`id_parent_folder`)
          REFERENCES `folders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `folders`
          DROP FOREIGN KEY `fk_9eef5b52fd6d0a1680220c056355837e`;");
  }
}