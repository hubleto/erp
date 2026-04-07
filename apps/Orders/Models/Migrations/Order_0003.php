<?php

namespace Hubleto\App\Community\Orders\Models\Migrations;

use Hubleto\Framework\Migration;

class Order_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `orders` add `id_document` int(8)');
    $this->db->execute('alter table `orders` add index(`id_document`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `orders` drop `id_document`');
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `orders` ADD CONSTRAINT `fk__orders__id_document` FOREIGN KEY (`id_document`)
      REFERENCES `documents` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `orders` DROP FOREIGN KEY `fk__orders__id_document`;");
  }
}