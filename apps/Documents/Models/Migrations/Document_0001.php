<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class Document_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `documents`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `documents` (
 `id` int(8) primary key auto_increment,
 `uid` varchar(255) ,
 `id_folder` int(8) NULL default NULL,
 `name` varchar(255) ,
 `file` varchar(255) ,
 `hyperlink` varchar(255) ,
 `origin_link` varchar(255) ,
 index `id` (`id`),
 index `id_folder` (`id_folder`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `documents`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `documents`
          ADD CONSTRAINT `fk_19b414ad9db15a0afa56e50f18bfe615`
          FOREIGN KEY (`id_folder`)
          REFERENCES `folders` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `documents`
          DROP FOREIGN KEY `fk_19b414ad9db15a0afa56e50f18bfe615`;");
  }
}