<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class ProjectRate_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("
      drop table if exists `projects_rates`;
      create table `projects_rates` (
        `id` int(8) primary key auto_increment,
        `id_project` int(8) NULL default NULL,
        `id_user` int(8) NULL default NULL,
        `rate` double(14,2) NULL default NULL,
        index `id` (`id`),
        index `id_project` (`id_project`),
        index `id_user` (`id_user`)
      ) ENGINE = InnoDB;
    ");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("drop table if exists `projects_rates`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `projects_rates` ADD CONSTRAINT `fk__projects_rates__id_project`
      FOREIGN KEY (`id_project`) REFERENCES `projects` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
      ALTER TABLE `projects_rates` ADD CONSTRAINT `fk__projects_rates__id_user`
      FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `projects_rates` DROP FOREIGN KEY `fk__projects_rates__id_project`;
      ALTER TABLE `projects_rates` DROP FOREIGN KEY `fk__projects_rates__id_user`;
    ");
  }
}