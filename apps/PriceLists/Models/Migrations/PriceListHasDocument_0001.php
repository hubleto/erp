<?php

namespace Hubleto\App\Community\PriceLists\Models\Migrations;

class PriceListHasDocument_0001 extends \Hubleto\Framework\Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_documents`;
      create table `price_lists_documents` (
      `id` int(8) primary key auto_increment,
      `id_price_list` int(8) NULL default NULL,
      `id_document` int(8) NULL default NULL,
      index `id` (`id`),
      index `id_price_list` (`id_price_list`),
      index `id_document` (`id_document`)
      ) ENGINE = InnoDB;
      set foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_documents`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_documents` ADD CONSTRAINT `fk_1f2b999e22a0ea5e20ae94d2a38b5043` FOREIGN KEY (`id_price_list`) REFERENCES `price_lists` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_documents` ADD CONSTRAINT `fk_81dc3661298edde1e280bccd3d2132fb` FOREIGN KEY (`id_document`) REFERENCES `documents` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_documents` DROP FOREIGN KEY `fk_1f2b999e22a0ea5e20ae94d2a38b5043`;
      ALTER TABLE `price_lists_documents` DROP FOREIGN KEY `fk_81dc3661298edde1e280bccd3d2132fb`;
    ");
  }
}
