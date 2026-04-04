<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class Document_0004 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `documents` add `model` varchar(255)');
    $this->db->execute('alter table `documents` add index (`model`)');

    $this->db->execute('alter table `documents` add `record_id` int(8)');
    $this->db->execute('alter table `documents` add index (`record_id`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `documents` drop `model`');
    $this->db->execute('alter table `documents` drop `record_id`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}