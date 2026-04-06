<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class DocumentReview_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `documents_reviews` change `comments` `comment` text');
    $this->db->execute('alter table `documents_reviews` add `id_review_result` int(8)');
    $this->db->execute('alter table `documents_reviews` add index (`id_review_result`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `documents_reviews` drop `id_review_result`');
    $this->db->execute('alter table `documents_reviews` change `comment` `comments` text`');
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `documents_reviews` ADD CONSTRAINT `fk__documents_reviews__id_review_result` FOREIGN KEY (`id_review_result`)
        REFERENCES `documents_review_results` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `documents_reviews` DROP FOREIGN KEY `fk__documents_reviews__id_review_result`");
  }
}