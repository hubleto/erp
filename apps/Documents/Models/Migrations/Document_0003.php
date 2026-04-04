<?php

namespace Hubleto\App\Community\Documents\Models\Migrations;

use Hubleto\Framework\Migration;

class Document_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("set foreign_key_checks = 0");
    $this->db->execute('alter table `documents` drop `is_public`');
    $this->db->execute('alter table `documents` drop `file`');
    $this->db->execute('alter table `documents` drop `hyperlink`');
    $this->db->execute('alter table `documents` drop `origin_link`');
    $this->db->execute("set foreign_key_checks = 1");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `documents` add `is_public` int(1)');
    $this->db->execute('alter table `documents` add index (`is_public`)');

    $this->db->execute('alter table `documents` add `file` varchar(255)');
    $this->db->execute('alter table `documents` add `hyperlink` varchar(255)');
    $this->db->execute('alter table `documents` add `origin_link` varchar(255)');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}