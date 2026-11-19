<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

// Activity for everyone: every user can see it, see the Activity model of the Calendar app.
class ProjectActivity_0003 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `project_activities` ADD COLUMN `for_everyone` int(1) NOT NULL DEFAULT 0 AFTER `attendees`");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `project_activities` DROP COLUMN `for_everyone`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
