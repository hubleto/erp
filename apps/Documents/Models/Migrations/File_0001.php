<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class File_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `files`;
      create table `files` (
        `id` int(8) primary key auto_increment,
        `uid` varchar(255),
        `id_folder` int(8) NULL default NULL,
        `name` varchar(255),
        `file` varchar(255),
        `hyperlink` varchar(255),
        `origin_link` varchar(255),
        `is_public` int(1),
        index `id` (`id`),
        index `id_folder` (`id_folder`),
        index `is_public` (`is_public`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `files`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `files` ADD CONSTRAINT `fk__id_folder` FOREIGN KEY (`id_folder`)
        REFERENCES `folders` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `folders` DROP FOREIGN KEY `fk_id_folder`");
  }
}