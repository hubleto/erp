<?php

namespace Hubleto\App\Community\PriceLists\Models\Migrations;

class Assignment_0001 extends \Hubleto\Framework\Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_assignments`;
      create table `price_lists_assignments` (
      `id` int(8) primary key auto_increment,
      `id_price_list` int(8) NULL default NULL,
      `id_customer` int(8) NULL default NULL,
      `id_customer_tag` int(8) NULL default NULL,
      `id_country` int(8) NULL default NULL,
      `id_supplier` int(8) NULL default NULL,
      `id_shop` int(8) NULL default NULL,
      `id_cash_register` int(8) NULL default NULL,
      `id_deal` int(8) NULL default NULL,
      `id_project` int(8) NULL default NULL,
      `valid_from` date ,
      `valid_to` date ,
      `priority` int(255) ,
      `is_default_for_target` int(1) ,
      index `id` (`id`),
      index `id_price_list` (`id_price_list`),
      index `id_customer` (`id_customer`),
      index `id_customer_tag` (`id_customer_tag`),
      index `id_country` (`id_country`),
      index `id_supplier` (`id_supplier`),
      index `id_shop` (`id_shop`),
      index `id_cash_register` (`id_cash_register`),
      index `id_deal` (`id_deal`),
      index `id_project` (`id_project`),
      index `valid_from` (`valid_from`),
      index `valid_to` (`valid_to`),
      index `priority` (`priority`),
      index `is_default_for_target` (`is_default_for_target`)
      ) ENGINE = InnoDB;
      set foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `price_lists_assignments`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_assignments` ADD CONSTRAINT `fk_9d59d9dda795968914837225590731ae` FOREIGN KEY (`id_price_list`) REFERENCES `price_lists` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_assignments` ADD CONSTRAINT `fk_70e8e3edf24a94e69ec78619a2003db3` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_assignments` ADD CONSTRAINT `fk_46c568fe259819ed1e8b99be33dc162a` FOREIGN KEY (`id_customer_tag`) REFERENCES `customer_tags` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_assignments` ADD CONSTRAINT `fk_51801a1fbe84ab3a7e5119daa7575930` FOREIGN KEY (`id_country`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_assignments` ADD CONSTRAINT `fk_107f67e3a54244c2c380130de5905593` FOREIGN KEY (`id_supplier`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_assignments` ADD CONSTRAINT `fk_4e297393ca1bf51d0a7a0b1f2c771205` FOREIGN KEY (`id_shop`) REFERENCES `shops` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_assignments` ADD CONSTRAINT `fk_eb939987dab0cf6d4d04cdc87bebefc1` FOREIGN KEY (`id_cash_register`) REFERENCES `cashdesk_cash_registers` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_assignments` ADD CONSTRAINT `fk_8c3cc254897633ba305606648e58c429` FOREIGN KEY (`id_deal`) REFERENCES `deals` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
      ALTER TABLE `price_lists_assignments` ADD CONSTRAINT `fk_5884f867c8799d81d514abb9ba19c56f` FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `price_lists_assignments` DROP FOREIGN KEY `fk_9d59d9dda795968914837225590731ae`;
      ALTER TABLE `price_lists_assignments` DROP FOREIGN KEY `fk_70e8e3edf24a94e69ec78619a2003db3`;
      ALTER TABLE `price_lists_assignments` DROP FOREIGN KEY `fk_46c568fe259819ed1e8b99be33dc162a`;
      ALTER TABLE `price_lists_assignments` DROP FOREIGN KEY `fk_51801a1fbe84ab3a7e5119daa7575930`;
      ALTER TABLE `price_lists_assignments` DROP FOREIGN KEY `fk_107f67e3a54244c2c380130de5905593`;
      ALTER TABLE `price_lists_assignments` DROP FOREIGN KEY `fk_4e297393ca1bf51d0a7a0b1f2c771205`;
      ALTER TABLE `price_lists_assignments` DROP FOREIGN KEY `fk_eb939987dab0cf6d4d04cdc87bebefc1`;
      ALTER TABLE `price_lists_assignments` DROP FOREIGN KEY `fk_8c3cc254897633ba305606648e58c429`;
      ALTER TABLE `price_lists_assignments` DROP FOREIGN KEY `fk_5884f867c8799d81d514abb9ba19c56f`;
    ");
  }
}
