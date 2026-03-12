<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

class LeadDocument_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `lead_documents`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `lead_documents` (
 `id` int(8) primary key auto_increment,
 `id_lead` int(8) NULL default NULL,
 `id_document` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_lead` (`id_lead`),
 index `id_document` (`id_document`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `lead_documents`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `lead_documents`
          ADD CONSTRAINT `fk_8ba4ea701b923abc5a539e2cfd9efbcb`
          FOREIGN KEY (`id_lead`)
          REFERENCES `leads` (`id`)
          ON DELETE CASCADE
          ON UPDATE CASCADE; ALTER TABLE `lead_documents`
          ADD CONSTRAINT `fk_0ee6e07c7b32517c0634836eb2f584e1`
          FOREIGN KEY (`id_document`)
          REFERENCES `documents` (`id`)
          ON DELETE CASCADE
          ON UPDATE CASCADE;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `lead_documents`
          DROP FOREIGN KEY `fk_8ba4ea701b923abc5a539e2cfd9efbcb`; ALTER TABLE `lead_documents`
          DROP FOREIGN KEY `fk_0ee6e07c7b32517c0634836eb2f584e1`;");
  }
}