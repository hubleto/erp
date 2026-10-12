<?php

namespace Hubleto\App\Community\PriceLists\Models\Migrations;

class PriceList_0001 extends \Hubleto\Framework\Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists`;
      create table `price_lists` (
      `id` int(8) primary key auto_increment,
      `code` varchar(255) ,
      `name` varchar(255) ,
      `description` text ,
      `type` int(255) ,
      `status` int(255) ,
      `id_currency` int(8) NULL default NULL,
      `prices_include_vat` int(1) ,
      `basis` int(255) ,
      `id_parent_price_list` int(8) NULL default NULL,
      `derive_method` int(255) ,
      `derive_value` decimal(14, 4) ,
      `rounding_policy` int(255) ,
      `rounding_step` decimal(14, 4) ,
      `rounding_ending` decimal(14, 4) ,
      `valid_from` date ,
      `valid_to` date ,
      `priority` int(255) ,
      `is_default` int(1) ,
      `id_workflow` int(8) NULL default NULL,
      `id_workflow_step` int(8) NULL default NULL,
      `id_owner` int(8) NULL default NULL,
      `id_manager` int(8) NULL default NULL,
      `shared_with` text ,
      `note` text ,
      index `id` (`id`),
      index `type` (`type`),
      index `status` (`status`),
      index `id_currency` (`id_currency`),
      index `prices_include_vat` (`prices_include_vat`),
      index `basis` (`basis`),
      index `id_parent_price_list` (`id_parent_price_list`),
      index `derive_method` (`derive_method`),
      index `rounding_policy` (`rounding_policy`),
      index `valid_from` (`valid_from`),
      index `valid_to` (`valid_to`),
      index `priority` (`priority`),
      index `is_default` (`is_default`),
      index `id_workflow` (`id_workflow`),
      index `id_workflow_step` (`id_workflow_step`),
      index `id_owner` (`id_owner`),
      index `id_manager` (`id_manager`)
      ) ENGINE = InnoDB;
      set foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists` ADD CONSTRAINT `fk_4cf2272016cb636c8f39f678940fe491` FOREIGN KEY (`id_currency`) REFERENCES `currencies` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
      ALTER TABLE `price_lists` ADD CONSTRAINT `fk_99d8bbb759eacabcc0198d78f4b22cf1` FOREIGN KEY (`id_parent_price_list`) REFERENCES `price_lists` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists` ADD CONSTRAINT `fk_17dd1d298e506d28fa52a8d594aa8a98` FOREIGN KEY (`id_workflow`) REFERENCES `workflows` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists` ADD CONSTRAINT `fk_86c149c216f82b7cd1462bbb6c0d51ef` FOREIGN KEY (`id_workflow_step`) REFERENCES `workflow_steps` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists` ADD CONSTRAINT `fk_6e71860296ec823911741841b1c56a11` FOREIGN KEY (`id_owner`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
      ALTER TABLE `price_lists` ADD CONSTRAINT `fk_2a2f4c48c626925c6f4c8d6eaeb29c9e` FOREIGN KEY (`id_manager`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists` DROP FOREIGN KEY `fk_4cf2272016cb636c8f39f678940fe491`;
      ALTER TABLE `price_lists` DROP FOREIGN KEY `fk_99d8bbb759eacabcc0198d78f4b22cf1`;
      ALTER TABLE `price_lists` DROP FOREIGN KEY `fk_17dd1d298e506d28fa52a8d594aa8a98`;
      ALTER TABLE `price_lists` DROP FOREIGN KEY `fk_86c149c216f82b7cd1462bbb6c0d51ef`;
      ALTER TABLE `price_lists` DROP FOREIGN KEY `fk_6e71860296ec823911741841b1c56a11`;
      ALTER TABLE `price_lists` DROP FOREIGN KEY `fk_2a2f4c48c626925c6f4c8d6eaeb29c9e`;
    ");
  }
}
