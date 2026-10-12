<?php

namespace Hubleto\App\Community\Settings\Models\Migrations;

class CurrencyRate_0001 extends \Hubleto\Framework\Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `currency_rates`;
      create table `currency_rates` (
      `id` int(8) primary key auto_increment,
      `id_currency` int(8) NULL default NULL,
      `rate_to_default` decimal(14, 8) ,
      `valid_from` date ,
      `source` varchar(255) ,
      index `id` (`id`),
      index `id_currency` (`id_currency`),
      index `valid_from` (`valid_from`)
      ) ENGINE = InnoDB;
      set foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `currency_rates`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `currency_rates` ADD CONSTRAINT `fk_017d25ff2e716aeedb14a2b15ee32d40` FOREIGN KEY (`id_currency`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `currency_rates` DROP FOREIGN KEY `fk_017d25ff2e716aeedb14a2b15ee32d40`;
    ");
  }
}
