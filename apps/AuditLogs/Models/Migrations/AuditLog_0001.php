<?php

namespace Hubleto\App\Community\AuditLogs\Models\Migrations;

use Hubleto\Framework\Migration;

class AuditLog_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `audit_logs`;
      set foreign_key_checks = 1;
    ");
    $this->db->execute("
      SET foreign_key_checks = 0;
      create table `audit_logs` (
        `id` int(8) primary key auto_increment,
        `datetime` datetime,
        `type` int(8),
        `context` varchar(255),
        `model` varchar(255),
        `record_id` int(8) NULL default NULL,
        `message` varchar(255),
        `priority` int(8),
        `id_user` int(8) NULL default NULL,
        `ip` varchar(255),
        index `id` (`id`),
        index `datetime` (`datetime`),
        index `type` (`type`),
        index `context` (`context`),
        index `model` (`model`),
        index `record_id` (`record_id`),
        index `priority` (`priority`),
        index `id_user` (`id_user`),
        index `ip` (`ip`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `audit_logs`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `audit_logs` ADD CONSTRAINT `fk__audit_logs__id_user` FOREIGN KEY (`id_user`)
        REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `audit_logs` DROP FOREIGN KEY `fk__audit_logs__id_user`;");
  }
}