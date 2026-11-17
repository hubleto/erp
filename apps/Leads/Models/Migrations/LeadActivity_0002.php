<?php

namespace Hubleto\App\Community\Leads\Models\Migrations;

use Hubleto\Framework\Migration;

// Attendees of the activity (JSON list of user ids), see the Activity model of the Calendar app.
class LeadActivity_0002 extends Migration
{
  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `lead_activities` ADD COLUMN `attendees` text NULL DEFAULT NULL AFTER `id_owner`");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `lead_activities` DROP COLUMN `attendees`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}
