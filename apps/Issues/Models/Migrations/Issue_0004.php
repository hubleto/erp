<?php

namespace Hubleto\App\Community\Issues\Models\Migrations;

use Hubleto\Framework\Migration;

class Issue_0004 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `issues` add `from` varchar(255)");
    $this->db->execute("alter table `issues` add index(`from`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `issues` drop `from`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}