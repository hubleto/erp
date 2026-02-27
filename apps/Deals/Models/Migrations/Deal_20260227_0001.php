<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class Deal_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deals`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `deals` (
 `id` int(8) primary key auto_increment,
 `identifier` varchar(255) ,
 `title` varchar(255) ,
 `id_customer` int(8) NULL default NULL,
 `id_contact` int(8) NULL default NULL,
 `id_lead` int(8) NULL default NULL,
 `version` int(255) ,
 `price_excl_vat` decimal(14, 4) ,
 `price_incl_vat` decimal(14, 4) ,
 `id_currency` int(8) NULL default NULL,
 `date_expected_close` date ,
 `id_owner` int(8) NULL default NULL,
 `id_manager` int(8) NULL default NULL,
 `shared_with` text ,
 `id_template_quotation` int(8) NULL default NULL,
 `customer_order_number` varchar(255) ,
 `id_workflow` int(8) NULL default NULL,
 `id_workflow_step` int(8) NULL default NULL,
 `shared_folder` varchar(255) ,
 `note` text ,
 `source_channel` int(255) ,
 `is_closed` int(1) ,
 `deal_result` int(255) ,
 `lost_reason` int(8) NULL default NULL,
 `date_result_update` datetime ,
 `is_new_customer` int(1) ,
 `business_type` int(255) ,
 `date_created` datetime ,
 index `id` (`id`),
 index `id_customer` (`id_customer`),
 index `id_contact` (`id_contact`),
 index `id_lead` (`id_lead`),
 index `version` (`version`),
 index `id_currency` (`id_currency`),
 index `date_expected_close` (`date_expected_close`),
 index `id_owner` (`id_owner`),
 index `id_manager` (`id_manager`),
 index `id_template_quotation` (`id_template_quotation`),
 index `id_workflow` (`id_workflow`),
 index `id_workflow_step` (`id_workflow_step`),
 index `source_channel` (`source_channel`),
 index `is_closed` (`is_closed`),
 index `deal_result` (`deal_result`),
 index `lost_reason` (`lost_reason`),
 index `date_result_update` (`date_result_update`),
 index `is_new_customer` (`is_new_customer`),
 index `business_type` (`business_type`),
 index `date_created` (`date_created`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `deals`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deals`
          ADD CONSTRAINT `fk_e1a6e2c565fd11a6fd4c7390bf38264e`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deals`
          ADD CONSTRAINT `fk_4701e9e6980a7abf6cda3c1b91330e51`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deals`
          ADD CONSTRAINT `fk_566b65783779d038bfce84d634a392fd`
          FOREIGN KEY (`id_lead`)
          REFERENCES `leads` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deals`
          ADD CONSTRAINT `fk_101f19e63f56cc1db8fd24c58338862f`
          FOREIGN KEY (`id_currency`)
          REFERENCES `currencies` (`id`)
          ON DELETE SET NULL
          ON UPDATE RESTRICT; ALTER TABLE `deals`
          ADD CONSTRAINT `fk_6d80097a4888ab11ad54c1de89eb7ce7`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deals`
          ADD CONSTRAINT `fk_aae59325112772b72c23af3b5d90c5bf`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deals`
          ADD CONSTRAINT `fk_3207a46e4aa8cfa317b4eeec4b62f8b7`
          FOREIGN KEY (`id_template_quotation`)
          REFERENCES `documents_templates` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deals`
          ADD CONSTRAINT `fk_109bacda6385499221866d57b905d898`
          FOREIGN KEY (`id_workflow`)
          REFERENCES `workflows` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deals`
          ADD CONSTRAINT `fk_f547f30d82900372612ef2ac930bc809`
          FOREIGN KEY (`id_workflow_step`)
          REFERENCES `workflow_steps` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `deals`
          ADD CONSTRAINT `fk_efe196bfcac46bef9b4bbcd2bc2b0e1c`
          FOREIGN KEY (`lost_reason`)
          REFERENCES `deal_lost_reasons` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deals`
          DROP FOREIGN KEY `fk_e1a6e2c565fd11a6fd4c7390bf38264e`; ALTER TABLE `deals`
          DROP FOREIGN KEY `fk_4701e9e6980a7abf6cda3c1b91330e51`; ALTER TABLE `deals`
          DROP FOREIGN KEY `fk_566b65783779d038bfce84d634a392fd`; ALTER TABLE `deals`
          DROP FOREIGN KEY `fk_101f19e63f56cc1db8fd24c58338862f`; ALTER TABLE `deals`
          DROP FOREIGN KEY `fk_6d80097a4888ab11ad54c1de89eb7ce7`; ALTER TABLE `deals`
          DROP FOREIGN KEY `fk_aae59325112772b72c23af3b5d90c5bf`; ALTER TABLE `deals`
          DROP FOREIGN KEY `fk_3207a46e4aa8cfa317b4eeec4b62f8b7`; ALTER TABLE `deals`
          DROP FOREIGN KEY `fk_109bacda6385499221866d57b905d898`; ALTER TABLE `deals`
          DROP FOREIGN KEY `fk_f547f30d82900372612ef2ac930bc809`; ALTER TABLE `deals`
          DROP FOREIGN KEY `fk_efe196bfcac46bef9b4bbcd2bc2b0e1c`;");
  }
}