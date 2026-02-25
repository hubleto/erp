<?php

namespace Hubleto\App\Community\TestApp\Models\Migrations;

use Hubleto\Framework\Interfaces\MigrationInterface;
use Hubleto\Framework\Migration;

class Migration_24_02_2026_0001_initial extends Migration
{

  public function installTables(): void
  {
    $this->db->execute("CREATE TABLE IF NOT EXISTS `test_customers` (
      `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
  }

  public function uninstallTables(): void
  {
    $this->db->execute("DROP TABLE IF EXISTS `test_customers`;");
  }

  public function installForeignKeys(): void
  {
//    $this->db->query("CREATE UNIQUE INDEX idx_customers_email ON customers(email);");
  }

  public function uninstallForeignKeys(): void
  {
//    $this->db->query("DROP INDEX idx_customers_email ON customers;");
  }
}