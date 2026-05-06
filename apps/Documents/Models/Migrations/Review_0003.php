<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class Review_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `documents_reviews` add `id_version` int(8)');
    $this->db->execute('alter table `documents_reviews` add index (`id_version`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `documents_reviews` drop `id_version`');
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `documents_reviews` ADD CONSTRAINT `fk__documents_reviews__id_version` FOREIGN KEY (`id_version`)
        REFERENCES `documents_versions` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `documents_reviews` DROP FOREIGN KEY `fk__documents_reviews__id_version`");
  }
}