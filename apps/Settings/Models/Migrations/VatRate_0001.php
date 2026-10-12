<?php

namespace Hubleto\App\Community\Settings\Models\Migrations;

class VatRate_0001 extends \Hubleto\Framework\Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `vat_rates`;
      create table `vat_rates` (
      `id` int(8) primary key auto_increment,
      `code` varchar(255) ,
      `name` varchar(255) ,
      `percent` decimal(14, 2) ,
      `type` int(255) ,
      `id_country` int(8) NULL default NULL,
      `valid_from` date ,
      `valid_to` date ,
      `is_default` int(1) ,
      `note` text ,
      index `id` (`id`),
      index `type` (`type`),
      index `id_country` (`id_country`),
      index `valid_from` (`valid_from`),
      index `valid_to` (`valid_to`),
      index `is_default` (`is_default`)
      ) ENGINE = InnoDB;
      set foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `vat_rates`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `vat_rates` ADD CONSTRAINT `fk_96ff288a77ae10bee2c85ff1cd0fe706` FOREIGN KEY (`id_country`) REFERENCES `countries` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `vat_rates` DROP FOREIGN KEY `fk_96ff288a77ae10bee2c85ff1cd0fe706`;
    ");
  }
}
