<?php

namespace Hubleto\App\Community\Invoices\Models\Migrations;

use Hubleto\Framework\Migration;

class Invoice_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `invoices` add `id_document` int(8)');
    $this->db->execute('alter table `invoices` add index(`id_document`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `invoices` drop `id_document`');
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `invoices` ADD CONSTRAINT `fk__invoices__id_document` FOREIGN KEY (`id_document`)
      REFERENCES `documents` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `invoices` DROP FOREIGN KEY `fk__invoices__id_document`;");
  }
}