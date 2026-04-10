<?php

namespace Hubleto\App\Community\Issues\Models\Migrations;

use Hubleto\Framework\Migration;

class Post_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `issues_posts` change `post` `content` text");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `issues_posts` change `content` `post` text");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}