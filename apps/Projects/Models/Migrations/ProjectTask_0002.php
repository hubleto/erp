<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class ProjectTask_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `projects_tasks` add unique index `id_task_unique` (`id_task`);");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `projects_tasks` drop index (`id_task_unique`);");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}