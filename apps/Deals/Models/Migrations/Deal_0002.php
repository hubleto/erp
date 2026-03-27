<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class Deal_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `deals` add `description_before` text');
    $this->db->execute('alter table `deals` add `description_after` text');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `deals` drop `description_before`');
    $this->db->execute('alter table `deals` drop `description_after`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}