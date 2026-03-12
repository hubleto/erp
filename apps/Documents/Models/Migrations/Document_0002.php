<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class Document_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `documents` add `is_public` int(1)');
    $this->db->execute('alter table `documents` add index (`is_public`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `documents` drop `is_public`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}