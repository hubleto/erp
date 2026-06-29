<?php

namespace Hubleto\App\Community\Contacts\Models\Migrations;

use Hubleto\Framework\Migration;

class ContactTag_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `contact_contact_tags`;
      create table `contact_contact_tags` (
        `id` int(8) primary key auto_increment,
        `id_contact` int(8) NULL default NULL,
        `id_tag` int(8) NULL default NULL,
        index `id` (`id`),
        index `id_contact` (`id_contact`),
        index `id_tag` (`id_tag`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `contact_contact_tags`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `contact_contact_tags` ADD CONSTRAINT `fk__contact_contact_tags__id_contact`
      FOREIGN KEY (`id_contact`) REFERENCES `contacts` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `contact_contact_tags` ADD CONSTRAINT `fk__contact_contact_tags__id_tag`
      FOREIGN KEY (`id_tag`) REFERENCES `contact_tags` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `contact_contact_tags`
      DROP FOREIGN KEY `fk__contact_contact_tags__id_contact`;
      
      ALTER TABLE `contact_contact_tags`
      DROP FOREIGN KEY `fk__contact_contact_tags__id_tag`;
    ");
  }
}