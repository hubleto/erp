<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

class Lead_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `leads` add `profile_link_1` varchar(255) after `phone`');
    $this->db->execute('alter table `leads` add `profile_link_2` varchar(255) after `profile_link_1`');
    $this->db->execute('alter table `leads` add `profile_link_3` varchar(255) after `profile_link_2`');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `leads` drop `profile_link_1`');
    $this->db->execute('alter table `leads` drop `profile_link_2`');
    $this->db->execute('alter table `leads` drop `profile_link_3`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}