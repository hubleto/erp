<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class DocumentReview_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `documents_reviews`;
      create table `documents_reviews` (
        `id` int(8) primary key auto_increment,
        `uid` varchar(255) ,
        `id_document` int(8) NULL default NULL,
        `requested_on` datetime,
        `id_requested_by` int(8) NULL default NULL,
        `reviewed_on` datetime,
        `id_reviewed_by` int(8) NULL default NULL,
        `comments` text,
        index `id` (`id`),
        index `id_document` (`id_document`),
        index `id_requested_by` (`id_requested_by`),
        index `id_reviewed_by` (`id_reviewed_by`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `documents_reviews`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `documents_reviews` ADD CONSTRAINT `fk__id_document` FOREIGN KEY (`id_document`)
        REFERENCES `documents` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
    $this->db->execute("
      ALTER TABLE `documents_reviews` ADD CONSTRAINT `fk__id_requested_by` FOREIGN KEY (`id_requested_by`)
        REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
    $this->db->execute("
      ALTER TABLE `documents_reviews` ADD CONSTRAINT `fk__id_reviewed_by` FOREIGN KEY (`id_reviewed_by`)
        REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `documents_reviews` DROP FOREIGN KEY `fk__id_document`");
    $this->db->execute("ALTER TABLE `documents_reviews` DROP FOREIGN KEY `fk__id_requested_by`");
    $this->db->execute("ALTER TABLE `documents_reviews` DROP FOREIGN KEY `fk__id_reviewed_by`");
  }
}