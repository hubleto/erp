<?php

namespace Hubleto\App\Community\HrRecruitment\Models\Migrations;

use Hubleto\Framework\Migration;

class Candidate_0001 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `hr_candidates` (
      `id` int(8) primary key auto_increment,
      `first_name` varchar(255),
      `last_name` varchar(255),
      `email` varchar(255),
      `phone` varchar(255),
      `source` varchar(255),
      `portfolio_url` varchar(255),
      `consent_to_store_data` int(1),
      `notes` text,
      INDEX `email` (`email`),
      INDEX `last_name` (`last_name`)
    ) ENGINE=InnoDB");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `hr_candidates`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}