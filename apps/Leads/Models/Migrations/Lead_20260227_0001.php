<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

class Lead_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `leads`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `leads` (
 `id` int(8) primary key auto_increment,
 `title` varchar(255) ,
 `id_customer` int(8) NULL default NULL,
 `id_contact` int(8) NULL default NULL,
 `price` decimal(14, 4) ,
 `id_currency` int(8) NULL default NULL,
 `score` int(255) ,
 `date_expected_close` date ,
 `id_owner` int(8) NULL default NULL,
 `id_manager` int(8) NULL default NULL,
 `id_team` int(8) NULL default NULL,
 `date_created` datetime ,
 `lost_reason` int(8) NULL default NULL,
 `shared_folder` varchar(255) ,
 `note` text ,
 `id_workflow` int(8) NULL default NULL,
 `id_workflow_step` int(8) NULL default NULL,
 `source_channel` int(255) ,
 `is_closed` int(1) ,
 index `id` (`id`),
 index `id_customer` (`id_customer`),
 index `id_contact` (`id_contact`),
 index `id_currency` (`id_currency`),
 index `score` (`score`),
 index `date_expected_close` (`date_expected_close`),
 index `id_owner` (`id_owner`),
 index `id_manager` (`id_manager`),
 index `id_team` (`id_team`),
 index `date_created` (`date_created`),
 index `lost_reason` (`lost_reason`),
 index `id_workflow` (`id_workflow`),
 index `id_workflow_step` (`id_workflow_step`),
 index `source_channel` (`source_channel`),
 index `is_closed` (`is_closed`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `leads`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `leads`
          ADD CONSTRAINT `fk_3771a0df5fb6546bd2198ac6c3ed7ec0`
          FOREIGN KEY (`id_customer`)
          REFERENCES `customers` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `leads`
          ADD CONSTRAINT `fk_37954b3c3f1a064d73ea08b9ce03efd9`
          FOREIGN KEY (`id_contact`)
          REFERENCES `contacts` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `leads`
          ADD CONSTRAINT `fk_fe81925df4bcf2e277243f5d688897b5`
          FOREIGN KEY (`id_currency`)
          REFERENCES `currencies` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `leads`
          ADD CONSTRAINT `fk_7e5c5acee22279d81bb854afb1ab62c7`
          FOREIGN KEY (`id_owner`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `leads`
          ADD CONSTRAINT `fk_84068dea9b75886b6b8a60b2d0772ef2`
          FOREIGN KEY (`id_manager`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `leads`
          ADD CONSTRAINT `fk_5a3c46238106750695aa13f2a4b9abff`
          FOREIGN KEY (`id_team`)
          REFERENCES `teams` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `leads`
          ADD CONSTRAINT `fk_f961e30941d6fb83cbfb668d6f6bc318`
          FOREIGN KEY (`lost_reason`)
          REFERENCES `lead_lost_reasons` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `leads`
          ADD CONSTRAINT `fk_596b5a2fc226f45255b59f8a1cec261f`
          FOREIGN KEY (`id_workflow`)
          REFERENCES `workflows` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `leads`
          ADD CONSTRAINT `fk_59f68e6002660b32d4e622642de7f2f6`
          FOREIGN KEY (`id_workflow_step`)
          REFERENCES `workflow_steps` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `leads`
          DROP FOREIGN KEY `fk_3771a0df5fb6546bd2198ac6c3ed7ec0`; ALTER TABLE `leads`
          DROP FOREIGN KEY `fk_37954b3c3f1a064d73ea08b9ce03efd9`; ALTER TABLE `leads`
          DROP FOREIGN KEY `fk_fe81925df4bcf2e277243f5d688897b5`; ALTER TABLE `leads`
          DROP FOREIGN KEY `fk_7e5c5acee22279d81bb854afb1ab62c7`; ALTER TABLE `leads`
          DROP FOREIGN KEY `fk_84068dea9b75886b6b8a60b2d0772ef2`; ALTER TABLE `leads`
          DROP FOREIGN KEY `fk_5a3c46238106750695aa13f2a4b9abff`; ALTER TABLE `leads`
          DROP FOREIGN KEY `fk_f961e30941d6fb83cbfb668d6f6bc318`; ALTER TABLE `leads`
          DROP FOREIGN KEY `fk_596b5a2fc226f45255b59f8a1cec261f`; ALTER TABLE `leads`
          DROP FOREIGN KEY `fk_59f68e6002660b32d4e622642de7f2f6`;");
  }
}