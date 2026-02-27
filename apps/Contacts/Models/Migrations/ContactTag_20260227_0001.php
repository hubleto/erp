<?php

namespace Hubleto\App\Community\Contacts\Models\Migrations;

use Hubleto\Framework\Migration;

class ContactTag_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `contact_contact_tags`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `contact_contact_tags` (
 `id` int(8) primary key auto_increment,
 `id_contact` int(8) NULL default NULL,
 `id_tag` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_contact` (`id_contact`),
 index `id_tag` (`id_tag`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `contact_contact_tags`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `contact_contact_tags`
          ADD CONSTRAINT `fk_cc78a61fa1c24a22b138ade1a178e6c3`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `contact_contact_tags`
          ADD CONSTRAINT `fk_e2810134ca4dac4d9bc85135ffac0b68`
          FOREIGN KEY (`id_tag`)
          REFERENCES `contact_tags` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `contact_contact_tags`
          DROP FOREIGN KEY `fk_cc78a61fa1c24a22b138ade1a178e6c3`; ALTER TABLE `contact_contact_tags`
          DROP FOREIGN KEY `fk_e2810134ca4dac4d9bc85135ffac0b68`;");
  }
}