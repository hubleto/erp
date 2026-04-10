<?php

namespace Hubleto\App\Community\Issues\Models\Migrations;

use Hubleto\Framework\Migration;

class Post_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `issues_posts`;
      create table `issues_posts` (
        `id` int(8) primary key auto_increment,
        `id_issue` int(8) NULL default NULL,
        `id_mail` int(8) NULL default NULL,
        `from` varchar(255) NULL default NULL,
        `post` text NULL default NULL,
        index `id` (`id`),
        index `id_issue` (`id_issue`),
        index `id_mail` (`id_mail`),
        index `from` (`from`)
      ) ENGINE = InnoDB;
      SET foreign_key_checks = 1;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("
      set foreign_key_checks = 0;
      drop table if exists `issues_posts`;
      set foreign_key_checks = 1;
    ");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `issues_posts`
      ADD CONSTRAINT `fk__issues_posts__id_issue`
      FOREIGN KEY (`id_issue`)
      REFERENCES `issues` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
      
      ALTER TABLE `issues_posts`
      ADD CONSTRAINT `fk__issues_posts__id_mail`
      FOREIGN KEY (`id_mail`)
      REFERENCES `mails` (`id`)
      ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `issues_posts` DROP FOREIGN KEY `fk__issues_posts__id_issue`;
      ALTER TABLE `issues_posts` DROP FOREIGN KEY `fk__issues_posts__id_mail`;
    ");
  }
}