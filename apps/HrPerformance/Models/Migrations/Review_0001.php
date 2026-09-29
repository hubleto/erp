<?php

namespace Hubleto\App\Community\HrPerformance\Models\Migrations;

use Hubleto\Framework\Migration;

class Review_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_reviews` (
      `id` int(8) primary key auto_increment,
      `id_user` int(8) NULL DEFAULT NULL,
      `id_reviewer` int(8) NULL DEFAULT NULL,
      `period` varchar(255),
      `date_reviewed` date,
      `score` decimal(14,2),
      `status` varchar(255),
      `summary` text,
      INDEX `id_user` (`id_user`),
      INDEX `id_reviewer` (`id_reviewer`),
      INDEX `period` (`period`),
      INDEX `date_reviewed` (`date_reviewed`),
      INDEX `status` (`status`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_reviews`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_reviews` ADD CONSTRAINT `fk__hr_reviews__id_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
    $this->db->execute("ALTER TABLE `hr_reviews` ADD CONSTRAINT `fk__hr_reviews__id_reviewer` FOREIGN KEY (`id_reviewer`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `hr_reviews` DROP FOREIGN KEY `fk__hr_reviews__id_user`");
    $this->db->execute("ALTER TABLE `hr_reviews` DROP FOREIGN KEY `fk__hr_reviews__id_reviewer`");
  }
}