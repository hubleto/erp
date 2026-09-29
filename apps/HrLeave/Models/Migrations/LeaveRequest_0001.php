<?php

namespace Hubleto\App\Community\HrLeave\Models\Migrations;

use Hubleto\Framework\Migration;

class LeaveRequest_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_leave_requests` (
      `id` int(8) primary key auto_increment,
      `id_user` int(8) NULL DEFAULT NULL,
      `id_leave_type` int(8) NULL DEFAULT NULL,
      `date_from` date,
      `date_to` date,
      `balance_year` int(8),
      `days_requested` decimal(14,2),
      `status` varchar(255),
      `id_approver` int(8) NULL DEFAULT NULL,
      `date_decided` date,
      `reason` text,
      INDEX `id_user` (`id_user`),
      INDEX `id_leave_type` (`id_leave_type`),
      INDEX `date_from` (`date_from`),
      INDEX `date_to` (`date_to`),
      INDEX `balance_year` (`balance_year`),
      INDEX `status` (`status`),
      INDEX `id_approver` (`id_approver`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_leave_requests`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_leave_requests` ADD CONSTRAINT `fk__hr_leave_requests__id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    $this->db->execute("ALTER TABLE `hr_leave_requests` ADD CONSTRAINT `fk__hr_leave_requests__id_leave_type` FOREIGN KEY (`id_leave_type`) REFERENCES `hr_leave_types` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    $this->db->execute("ALTER TABLE `hr_leave_requests` ADD CONSTRAINT `fk__hr_leave_requests__id_approver` FOREIGN KEY (`id_approver`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_leave_requests` DROP FOREIGN KEY `fk__hr_leave_requests__id_user`");
    $this->db->execute("ALTER TABLE `hr_leave_requests` DROP FOREIGN KEY `fk__hr_leave_requests__id_leave_type`");
    $this->db->execute("ALTER TABLE `hr_leave_requests` DROP FOREIGN KEY `fk__hr_leave_requests__id_approver`");
  }
}