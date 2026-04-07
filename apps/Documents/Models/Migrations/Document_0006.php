<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class Document_0006 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `documents` add `id_workflow` int(8)');
    $this->db->execute('alter table `documents` add index (`id_workflow`)');

    $this->db->execute('alter table `documents` add `id_workflow_step` int(8)');
    $this->db->execute('alter table `documents` add index (`id_workflow_step`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `documents` drop `id_workflow`');
    $this->db->execute('alter table `documents` drop `id_workflow_step`');
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `documents` ADD CONSTRAINT `fk__documents__id_workflow` FOREIGN KEY (`id_workflow`)
        REFERENCES `workflows` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
    $this->db->execute("
      ALTER TABLE `documents` ADD CONSTRAINT `fk__documents__id_workflow_step` FOREIGN KEY (`id_workflow_step`)
        REFERENCES `workflow_steps` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `documents` DROP FOREIGN KEY `fk__documents__id_workflow`");
    $this->db->execute("ALTER TABLE `documents` DROP FOREIGN KEY `fk__documents__id_workflow_step`");
  }
}