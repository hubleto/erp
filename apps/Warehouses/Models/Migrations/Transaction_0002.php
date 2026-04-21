<?php

namespace Hubleto\App\Community\Warehouses\Models\Migrations;

use Hubleto\Framework\Migration;

class Transaction_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `warehouses_transactions` add `id_order` int(8)");
    $this->db->execute("alter table `warehouses_transactions` add index(`id_order`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `warehouses_transactions` drop index `id_order`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `warehouses_transactions`
      ADD CONSTRAINT `fk__warehouses_transactions__id_order`
      FOREIGN KEY (`id_order`) REFERENCES `orders` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `warehouses_transactions`
      DROP FOREIGN KEY `fk__warehouses_transactions__id_order`
    ");
  }
}