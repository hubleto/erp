<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class Deal_0004 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `deals` add `id_document` int(8)');
    $this->db->execute('alter table `deals` add index(`id_document`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `deals` drop `id_document`');
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `deals` ADD CONSTRAINT `fk__deals__id_document` FOREIGN KEY (`id_document`)
      REFERENCES `documents` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `deals` DROP FOREIGN KEY `fk__deals__id_document`;");
  }
}