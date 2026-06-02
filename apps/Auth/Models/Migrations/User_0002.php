<?php

namespace Hubleto\App\Community\Auth\Models\Migrations;

use Hubleto\Framework\Migration;

class User_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `users` add `force_signout` int(1) after `is_active`');
    $this->db->execute('alter table `users` add index (`force_signout`)');
    $this->db->execute('update `users` set `force_signout` = 0 where `force_signout` is null');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `users` drop `force_signout`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
