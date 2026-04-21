<?php

namespace Hubleto\App\Community\Warehouses\Models\Migrations;

use Hubleto\Framework\Migration;

class TransactionItem_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `warehouses_transactions_items` add `id_location_old` int(8)");
    $this->db->execute("alter table `warehouses_transactions_items` add index(`id_location_old`)");

    $this->db->execute("alter table `warehouses_transactions_items` add `id_location_new` int(8)");
    $this->db->execute("alter table `warehouses_transactions_items` add index(`id_location_new`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `warehouses_transactions_items` drop `id_location_old` int(8)");
    $this->db->execute("alter table `warehouses_transactions_items` drop `id_location_new` int(8)");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `warehouses_transactions_items`
      ADD CONSTRAINT `fk__warehouses_transactions_items__id_location_old`
      FOREIGN KEY (`id_location_old`) REFERENCES `warehouses_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
    $this->db->execute("
      ALTER TABLE `warehouses_transactions_items`
      ADD CONSTRAINT `fk__warehouses_transactions_items__id_location_new`
      FOREIGN KEY (`id_location_new`) REFERENCES `warehouses_locations` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `warehouses_transactions_items`
      DROP FOREIGN KEY `fk__warehouses_transactions_items__id_location_old`
    ");
    $this->db->execute("
      ALTER TABLE `warehouses_transactions_items`
      DROP FOREIGN KEY `fk__warehouses_transactions_items__id_location_new`
    ");
  }
}