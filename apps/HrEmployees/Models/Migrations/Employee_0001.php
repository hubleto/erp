<?php

namespace Hubleto\App\Community\HrEmployees\Models\Migrations;

use Hubleto\Framework\Migration;

class Employee_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_employees` (
      `id` int(8) primary key auto_increment,
      `id_user` int(8) NULL DEFAULT NULL,
      `employee_number` varchar(255),
      `job_title` varchar(255),
      `id_team` int(8) NULL DEFAULT NULL,
      `id_manager` int(8) NULL DEFAULT NULL,
      `employment_type` varchar(255),
      `employment_status` varchar(255),
      `date_hired` date,
      `date_ended` date,
      `work_location` varchar(255),
      `notes` text,
      INDEX `id_user` (`id_user`),
      INDEX `employee_number` (`employee_number`),
      INDEX `id_team` (`id_team`),
      INDEX `id_manager` (`id_manager`),
      INDEX `employment_status` (`employment_status`),
      INDEX `date_hired` (`date_hired`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_employees`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_employees` ADD CONSTRAINT `fk__hr_employees__id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    $this->db->execute("ALTER TABLE `hr_employees` ADD CONSTRAINT `fk__hr_employees__id_team` FOREIGN KEY (`id_team`) REFERENCES `teams` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    $this->db->execute("ALTER TABLE `hr_employees` ADD CONSTRAINT `fk__hr_employees__id_manager` FOREIGN KEY (`id_manager`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_employees` DROP FOREIGN KEY `fk__hr_employees__id_user`");
    $this->db->execute("ALTER TABLE `hr_employees` DROP FOREIGN KEY `fk__hr_employees__id_team`");
    $this->db->execute("ALTER TABLE `hr_employees` DROP FOREIGN KEY `fk__hr_employees__id_manager`");
  }
}