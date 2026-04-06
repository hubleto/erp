<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class Document_0005 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `documents` add `created_on` datetime');
    $this->db->execute('alter table `documents` add index (`created_on`)');

    $this->db->execute('alter table `documents` add `id_created_by` int(8)');
    $this->db->execute('alter table `documents` add index (`id_created_by`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `documents` drop `created_on`');
    $this->db->execute('alter table `documents` drop `id_created_by`');
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `documents` ADD CONSTRAINT `fk__documents__id_created_by` FOREIGN KEY (`id_created_by`)
        REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `documents` DROP FOREIGN KEY `fk__documents__id_created_by`");
  }
}