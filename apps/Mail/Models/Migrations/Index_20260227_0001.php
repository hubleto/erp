<?php

namespace Hubleto\App\Community\Mail\Models\Migrations;

use Hubleto\Framework\Migration;

class Index_20260227_0001 extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `mails_index`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `mails_index` (
 `id` int(8) primary key auto_increment,
 `id_mail` int(8) NULL default NULL,
 `id_from` int(8) NULL default NULL,
 `id_to` int(8) NULL default NULL,
 `id_cc` int(8) NULL default NULL,
 `id_bcc` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_mail` (`id_mail`),
 index `id_from` (`id_from`),
 index `id_to` (`id_to`),
 index `id_cc` (`id_cc`),
 index `id_bcc` (`id_bcc`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `mails_index`;
set foreign_key_checks = 1;");
  }

  public function installForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `mails_index`
          ADD CONSTRAINT `fk_ed3606ccbffa91327e8898866a1bec6b`
          FOREIGN KEY (`id_mail`)
          REFERENCES `mails` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `mails_index`
          ADD CONSTRAINT `fk_816da6cd1880f5b99b7df69a8419b69d`
          FOREIGN KEY (`id_from`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `mails_index`
          ADD CONSTRAINT `fk_16181f42b16bac573301063af321c51b`
          FOREIGN KEY (`id_to`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `mails_index`
          ADD CONSTRAINT `fk_7eb59907d4e75252ae7a2d0fb887da82`
          FOREIGN KEY (`id_cc`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `mails_index`
          ADD CONSTRAINT `fk_2512b190924683207db01f400a682204`
          FOREIGN KEY (`id_bcc`)
          REFERENCES `users` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function uninstallForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `mails_index`
          DROP FOREIGN KEY `fk_ed3606ccbffa91327e8898866a1bec6b`; ALTER TABLE `mails_index`
          DROP FOREIGN KEY `fk_816da6cd1880f5b99b7df69a8419b69d`; ALTER TABLE `mails_index`
          DROP FOREIGN KEY `fk_16181f42b16bac573301063af321c51b`; ALTER TABLE `mails_index`
          DROP FOREIGN KEY `fk_7eb59907d4e75252ae7a2d0fb887da82`; ALTER TABLE `mails_index`
          DROP FOREIGN KEY `fk_2512b190924683207db01f400a682204`;");
  }
}