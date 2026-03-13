<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

class Lead_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `leads` add `email` varchar(255) after `id`');
    $this->db->execute('alter table `leads` add `phone` varchar(255) after `email`');
    $this->db->execute('alter table `leads` add index (`email`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `leads` drop `email`');
    $this->db->execute('alter table `leads` drop `phone`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}