<?php

namespace Hubleto\App\Community\Deals\Models\Migrations;

use Hubleto\Framework\Migration;

class Deal_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `deals` add `id_template` int(8)');
    $this->db->execute('alter table `deals` add `pdf` varchar(255)');
    $this->db->execute('alter table `deals` add index (`id_template`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `deals` drop `id_template`');
    $this->db->execute('alter table `deals` drop `pdf`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}