<?php

namespace Hubleto\App\Community\HrPerformance\Models\Migrations;

use Hubleto\Framework\Migration;

class Goal_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_goals` (
      `id` int(8) primary key auto_increment,
      `id_user` int(8) NULL DEFAULT NULL,
      `title` varchar(255),
      `description` text,
      `date_due` date,
      `progress` int(8),
      `status` varchar(255),
      INDEX `id_user` (`id_user`),
      INDEX `date_due` (`date_due`),
      INDEX `status` (`status`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_goals`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_goals` ADD CONSTRAINT `fk__hr_goals__id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_goals` DROP FOREIGN KEY `fk__hr_goals__id_user`");
  }
}