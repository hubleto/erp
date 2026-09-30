<?php

namespace Hubleto\App\Community\HrLeave\Models\Migrations;

use Hubleto\Framework\Migration;

class Leave_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_leaves` (
      `id` int(8) primary key auto_increment,
      `id_user` int(8) NULL DEFAULT NULL,
      `id_leave_type` int(8) NULL DEFAULT NULL,
      `year` int(8),
      `days_entitled` decimal(14,2),
      `days_carried_over` decimal(14,2),
      INDEX `id_user` (`id_user`),
      INDEX `id_leave_type` (`id_leave_type`),
      INDEX `year` (`year`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_leaves`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_leaves` ADD CONSTRAINT `fk__hr_leaves__id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    $this->db->execute("ALTER TABLE `hr_leaves` ADD CONSTRAINT `fk__hr_leaves__id_leave_type` FOREIGN KEY (`id_leave_type`) REFERENCES `hr_leave_types` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_leaves` DROP FOREIGN KEY `fk__hr_leaves__id_user`");
    $this->db->execute("ALTER TABLE `hr_leaves` DROP FOREIGN KEY `fk__hr_leaves__id_leave_type`");
  }
}