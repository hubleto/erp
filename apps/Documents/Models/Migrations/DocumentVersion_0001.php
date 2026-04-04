<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class DocumentVersion_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `documents_versions`;
      create table `documents_versions` (
        `id` int(8) primary key auto_increment,
        `uid` varchar(255) ,
        `id_document` int(8) NULL default NULL,
        `file` varchar(255) ,
        index `id` (`id`),
        index `id_document` (`id_document`))
        ENGINE = InnoDB
      ;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `documents_versions`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `documents_versions` ADD CONSTRAINT `fk__id_document` FOREIGN KEY (`id_document`)
        REFERENCES `documents` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `documents` DROP FOREIGN KEY `fk__id_document`");
  }
}