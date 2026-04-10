<?php

namespace Hubleto\App\Community\Issues\Models\Migrations;

use Hubleto\Framework\Migration;

class Post_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `issues_posts` add `thread_uid` varchar(255)");
    $this->db->execute("alter table `issues_posts` add index(`thread_uid`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `issues_posts` drop `thread_uid`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}