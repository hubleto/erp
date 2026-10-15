<?php

namespace Hubleto\App\Community\Auth\Models\Migrations;

use Hubleto\Framework\Migration;

class User_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `users` add `password_last_change` datetime');
    $this->db->execute('alter table `users` add index (`password_last_change`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `users` drop `password_last_change`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
