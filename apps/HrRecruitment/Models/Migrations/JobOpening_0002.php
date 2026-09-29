<?php

namespace Hubleto\App\Community\HrRecruitment\Models\Migrations;

use Hubleto\Framework\Migration;

class JobOpening_0002 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `hr_job_openings`
      CHANGE `employment_type` `employment_type_legacy` varchar(255) NULL DEFAULT NULL,
      CHANGE `location` `location_legacy` varchar(255) NULL DEFAULT NULL,
      CHANGE `date_opened` `date_opened_legacy` date NULL DEFAULT NULL,
      ADD `id_employment_type` int(8) NULL DEFAULT NULL,
      ADD `id_work_location` int(8) NULL DEFAULT NULL,
      ADD `id_opening_date` int(8) NULL DEFAULT NULL,
      ADD INDEX `id_employment_type` (`id_employment_type`),
      ADD INDEX `id_work_location` (`id_work_location`),
      ADD INDEX `id_opening_date` (`id_opening_date`),
      DROP INDEX `date_opened`");

    $this->db->execute("INSERT INTO `hr_recruitment_employment_types` (`name`)
      SELECT DISTINCT j.`employment_type_legacy` FROM `hr_job_openings` j
      WHERE j.`employment_type_legacy` IS NOT NULL AND j.`employment_type_legacy` <> ''
      AND NOT EXISTS (SELECT 1 FROM `hr_recruitment_employment_types` t WHERE t.`name` = j.`employment_type_legacy`)");
    $this->db->execute("INSERT INTO `hr_recruitment_work_locations` (`name`)
      SELECT DISTINCT j.`location_legacy` FROM `hr_job_openings` j
      WHERE j.`location_legacy` IS NOT NULL AND j.`location_legacy` <> ''
      AND NOT EXISTS (SELECT 1 FROM `hr_recruitment_work_locations` l WHERE l.`name` = j.`location_legacy`)");
    $this->db->execute("INSERT INTO `hr_recruitment_opening_dates` (`name`, `opened_on`)
      SELECT DISTINCT DATE_FORMAT(j.`date_opened_legacy`, '%Y-%m-%d'), j.`date_opened_legacy`
      FROM `hr_job_openings` j
      WHERE j.`date_opened_legacy` IS NOT NULL
      AND NOT EXISTS (SELECT 1 FROM `hr_recruitment_opening_dates` d WHERE d.`opened_on` = j.`date_opened_legacy`)");

    $this->db->execute("UPDATE `hr_job_openings` j JOIN `hr_recruitment_employment_types` t ON t.`name` = j.`employment_type_legacy` SET j.`id_employment_type` = t.`id`");
    $this->db->execute("UPDATE `hr_job_openings` j JOIN `hr_recruitment_work_locations` l ON l.`name` = j.`location_legacy` SET j.`id_work_location` = l.`id`");
    $this->db->execute("UPDATE `hr_job_openings` j JOIN `hr_recruitment_opening_dates` d ON d.`opened_on` = j.`date_opened_legacy` SET j.`id_opening_date` = d.`id`");

    $this->db->execute("ALTER TABLE `hr_job_openings`
      DROP COLUMN `employment_type_legacy`,
      DROP COLUMN `location_legacy`,
      DROP COLUMN `date_opened_legacy`");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `hr_job_openings`
      ADD `employment_type_legacy` varchar(255) NULL DEFAULT NULL,
      ADD `location_legacy` varchar(255) NULL DEFAULT NULL,
      ADD `date_opened_legacy` date NULL DEFAULT NULL");
    $this->db->execute("UPDATE `hr_job_openings` j LEFT JOIN `hr_recruitment_employment_types` t ON t.`id` = j.`id_employment_type` SET j.`employment_type_legacy` = t.`name`");
    $this->db->execute("UPDATE `hr_job_openings` j LEFT JOIN `hr_recruitment_work_locations` l ON l.`id` = j.`id_work_location` SET j.`location_legacy` = l.`name`");
    $this->db->execute("UPDATE `hr_job_openings` j LEFT JOIN `hr_recruitment_opening_dates` d ON d.`id` = j.`id_opening_date` SET j.`date_opened_legacy` = d.`opened_on`");
    $this->db->execute("ALTER TABLE `hr_job_openings`
      DROP COLUMN `id_employment_type`,
      DROP COLUMN `id_work_location`,
      DROP COLUMN `id_opening_date`,
      ADD INDEX `date_opened` (`date_opened_legacy`),
      CHANGE `employment_type_legacy` `employment_type` varchar(255) NULL DEFAULT NULL,
      CHANGE `location_legacy` `location` varchar(255) NULL DEFAULT NULL,
      CHANGE `date_opened_legacy` `date_opened` date NULL DEFAULT NULL");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute('ALTER TABLE `hr_job_openings` ADD CONSTRAINT `fk__hr_job_openings__id_employment_type` FOREIGN KEY (`id_employment_type`) REFERENCES `hr_recruitment_employment_types` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
    $this->db->execute('ALTER TABLE `hr_job_openings` ADD CONSTRAINT `fk__hr_job_openings__id_work_location` FOREIGN KEY (`id_work_location`) REFERENCES `hr_recruitment_work_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
    $this->db->execute('ALTER TABLE `hr_job_openings` ADD CONSTRAINT `fk__hr_job_openings__id_opening_date` FOREIGN KEY (`id_opening_date`) REFERENCES `hr_recruitment_opening_dates` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT');
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute('ALTER TABLE `hr_job_openings` DROP FOREIGN KEY `fk__hr_job_openings__id_employment_type`');
    $this->db->execute('ALTER TABLE `hr_job_openings` DROP FOREIGN KEY `fk__hr_job_openings__id_work_location`');
    $this->db->execute('ALTER TABLE `hr_job_openings` DROP FOREIGN KEY `fk__hr_job_openings__id_opening_date`');
  }
}