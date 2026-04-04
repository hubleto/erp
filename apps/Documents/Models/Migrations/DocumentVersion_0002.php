<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class DocumentVersion_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `documents_versions` add `version` int(8)');
    $this->db->execute('alter table `documents_versions` add index (`version`)');

    $this->db->execute('alter table `documents_versions` add `created_on` datetime');
    $this->db->execute('alter table `documents_versions` add index (`created_on`)');

    $this->db->execute('alter table `documents_versions` add `id_created_by` int(8)');
    $this->db->execute('alter table `documents_versions` add index (`id_created_by`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `documents_versions` drop `version`');
    $this->db->execute('alter table `documents_versions` drop `created_on`');
    $this->db->execute('alter table `documents_versions` drop `id_created_by`');
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `documents_versions` ADD CONSTRAINT `fk__id_created_by` FOREIGN KEY (`id_created_by`)
        REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `documents_versions` DROP FOREIGN KEY `fk__id_created_by`");
  }
}