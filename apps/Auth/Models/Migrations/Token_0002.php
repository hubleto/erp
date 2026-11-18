<?php

namespace Hubleto\App\Community\Auth\Models\Migrations;

use Hubleto\Framework\Migration;

/**
 * Adds counter of failed verification attempts. Used by the second authentication step
 * (6-digit sign-in code sent by email) to limit brute-force guessing of the code.
 */
class Token_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `tokens` add `attempts` int(8) not null default 0');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `tokens` drop `attempts`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
