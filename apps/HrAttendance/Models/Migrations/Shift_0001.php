<?php

namespace Hubleto\App\Community\HrAttendance\Models\Migrations;

use Hubleto\Framework\Migration;

class Shift_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_shifts` (
      `id` int(8) primary key auto_increment,
      `id_user` int(8) NULL DEFAULT NULL,
      `date_start` datetime,
      `date_end` datetime,
      `location` varchar(255),
      `status` varchar(255),
      INDEX `id_user` (`id_user`),
      INDEX `date_start` (`date_start`),
      INDEX `date_end` (`date_end`),
      INDEX `status` (`status`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_shifts`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_shifts` ADD CONSTRAINT `fk__hr_shifts__id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_shifts` DROP FOREIGN KEY `fk__hr_shifts__id_user`");
  }
}