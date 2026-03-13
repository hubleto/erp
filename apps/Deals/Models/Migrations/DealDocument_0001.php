<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class DealDocument_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deal_documents`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `deal_documents` (
 `id` int(8) primary key auto_increment,
 `id_deal` int(8) NULL default NULL,
 `id_document` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_deal` (`id_deal`),
 index `id_document` (`id_document`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deal_documents`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deal_documents`
          ADD CONSTRAINT `fk_2dcb03df8c6cf5b4eadc8767b2e18598`
          FOREIGN KEY (`id_deal`)
          REFERENCES `deals` (`id`)
          ON DELETE CASCADE
          ON UPDATE CASCADE; ALTER TABLE `deal_documents`
          ADD CONSTRAINT `fk_f482c547b2b5f5570fa39bfb17a19b6c`
          FOREIGN KEY (`id_document`)
          REFERENCES `documents` (`id`)
          ON DELETE CASCADE
          ON UPDATE CASCADE;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deal_documents`
          DROP FOREIGN KEY `fk_2dcb03df8c6cf5b4eadc8767b2e18598`; ALTER TABLE `deal_documents`
          DROP FOREIGN KEY `fk_f482c547b2b5f5570fa39bfb17a19b6c`;");
  }
}