<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class ProjectDeal_20260227_0001 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_deals`;
set foreign_key_checks = 1;");
    $this->db->execute("SET foreign_key_checks = 0;
create table `projects_deals` (
 `id` int(8) primary key auto_increment,
 `id_project` int(8) NULL default NULL,
 `id_deal` int(8) NULL default NULL,
 index `id` (`id`),
 index `id_project` (`id_project`),
 index `id_deal` (`id_deal`)) ENGINE = InnoDB;
SET foreign_key_checks = 1;");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0;
drop table if exists `projects_deals`;
set foreign_key_checks = 1;");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_deals`
          ADD CONSTRAINT `fk_ca278ea4d471825b4336337a32702b76`
          FOREIGN KEY (`id_project`)
          REFERENCES `projects` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT; ALTER TABLE `projects_deals`
          ADD CONSTRAINT `fk_92a0945098ad6b7c8422eabdbcf6dffe`
          FOREIGN KEY (`id_deal`)
          REFERENCES `deals` (`id`)
          ON DELETE RESTRICT
          ON UPDATE RESTRICT;");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `projects_deals`
          DROP FOREIGN KEY `fk_ca278ea4d471825b4336337a32702b76`; ALTER TABLE `projects_deals`
          DROP FOREIGN KEY `fk_92a0945098ad6b7c8422eabdbcf6dffe`;");
  }
}