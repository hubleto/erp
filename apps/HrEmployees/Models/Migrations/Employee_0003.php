<?php

namespace Hubleto\App\Community\HrEmployees\Models\Migrations;

use Hubleto\Framework\Migration;

class Employee_0003 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `hr_employees`
      CHANGE `employment_type` `employment_type_legacy` varchar(255) NULL DEFAULT NULL,
      CHANGE `employment_status` `employment_status_legacy` varchar(255) NULL DEFAULT NULL,
      CHANGE `work_location` `work_location_legacy` varchar(255) NULL DEFAULT NULL,
      ADD `id_employment_type` int(8) NULL DEFAULT NULL,
      ADD `id_employment_status` int(8) NULL DEFAULT NULL,
      ADD `id_work_location` int(8) NULL DEFAULT NULL,
      ADD INDEX `id_employment_type` (`id_employment_type`),
      ADD INDEX `id_employment_status` (`id_employment_status`),
      ADD INDEX `id_work_location` (`id_work_location`),
      DROP INDEX `employment_status`");

    $this->db->execute("INSERT INTO `hr_employment_types` (`name`)
      SELECT DISTINCT e.`employment_type_legacy` FROM `hr_employees` e
      WHERE e.`employment_type_legacy` IS NOT NULL AND e.`employment_type_legacy` <> ''
      AND NOT EXISTS (SELECT 1 FROM `hr_employment_types` t WHERE t.`name` = e.`employment_type_legacy`)");
    $this->db->execute("INSERT INTO `hr_employment_statuses` (`name`)
      SELECT DISTINCT e.`employment_status_legacy` FROM `hr_employees` e
      WHERE e.`employment_status_legacy` IS NOT NULL AND e.`employment_status_legacy` <> ''
      AND NOT EXISTS (SELECT 1 FROM `hr_employment_statuses` s WHERE s.`name` = e.`employment_status_legacy`)");
    $this->db->execute("INSERT INTO `hr_work_locations` (`name`)
      SELECT DISTINCT e.`work_location_legacy` FROM `hr_employees` e
      WHERE e.`work_location_legacy` IS NOT NULL AND e.`work_location_legacy` <> ''
      AND NOT EXISTS (SELECT 1 FROM `hr_work_locations` l WHERE l.`name` = e.`work_location_legacy`)");

    $this->db->execute("UPDATE `hr_employees` e JOIN `hr_employment_types` t ON t.`name` = e.`employment_type_legacy` SET e.`id_employment_type` = t.`id`");
    $this->db->execute("UPDATE `hr_employees` e JOIN `hr_employment_statuses` s ON s.`name` = e.`employment_status_legacy` SET e.`id_employment_status` = s.`id`");
    $this->db->execute("UPDATE `hr_employees` e JOIN `hr_work_locations` l ON l.`name` = e.`work_location_legacy` SET e.`id_work_location` = l.`id`");

    $this->db->execute("ALTER TABLE `hr_employees`
      DROP COLUMN `employment_type_legacy`,
      DROP COLUMN `employment_status_legacy`,
      DROP COLUMN `work_location_legacy`");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `hr_employees`
      ADD `employment_type_legacy` varchar(255) NULL DEFAULT NULL,
      ADD `employment_status_legacy` varchar(255) NULL DEFAULT NULL,
      ADD `work_location_legacy` varchar(255) NULL DEFAULT NULL");
    $this->db->execute("UPDATE `hr_employees` e LEFT JOIN `hr_employment_types` t ON t.`id` = e.`id_employment_type` SET e.`employment_type_legacy` = t.`name`");
    $this->db->execute("UPDATE `hr_employees` e LEFT JOIN `hr_employment_statuses` s ON s.`id` = e.`id_employment_status` SET e.`employment_status_legacy` = s.`name`");
    $this->db->execute("UPDATE `hr_employees` e LEFT JOIN `hr_work_locations` l ON l.`id` = e.`id_work_location` SET e.`work_location_legacy` = l.`name`");
    $this->db->execute("ALTER TABLE `hr_employees`
      DROP COLUMN `id_employment_type`,
      DROP COLUMN `id_employment_status`,
      DROP COLUMN `id_work_location`,
      ADD INDEX `employment_status` (`employment_status_legacy`),
      CHANGE `employment_type_legacy` `employment_type` varchar(255) NULL DEFAULT NULL,
      CHANGE `employment_status_legacy` `employment_status` varchar(255) NULL DEFAULT NULL,
      CHANGE `work_location_legacy` `work_location` varchar(255) NULL DEFAULT NULL");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute('ALTER TABLE `hr_employees` ADD CONSTRAINT `fk__hr_employees__id_employment_type` FOREIGN KEY (`id_employment_type`) REFERENCES `hr_employment_types` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
    $this->db->execute('ALTER TABLE `hr_employees` ADD CONSTRAINT `fk__hr_employees__id_employment_status` FOREIGN KEY (`id_employment_status`) REFERENCES `hr_employment_statuses` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
    $this->db->execute('ALTER TABLE `hr_employees` ADD CONSTRAINT `fk__hr_employees__id_work_location` FOREIGN KEY (`id_work_location`) REFERENCES `hr_work_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute('ALTER TABLE `hr_employees` DROP FOREIGN KEY `fk__hr_employees__id_employment_type`');
    $this->db->execute('ALTER TABLE `hr_employees` DROP FOREIGN KEY `fk__hr_employees__id_employment_status`');
    $this->db->execute('ALTER TABLE `hr_employees` DROP FOREIGN KEY `fk__hr_employees__id_work_location`');
  }
}