<?php

namespace Hubleto\App\Community\Projects\Models\Migrations;

use Hubleto\Framework\Migration;

class Milestone_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `projects_milestones` add `id_responsible` int(8)");
    $this->db->execute("ALTER TABLE `projects_milestones` add index(`id_responsible`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("ALTER TABLE `projects_milestones` drop `id_responsible`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `projects_milestones`
      ADD CONSTRAINT `fk__projects_milestones__id_responsible` FOREIGN KEY (`id_responsible`)
      REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `projects_milestones`
      DROP FOREIGN KEY `fk__projects_milestones__id_responsible`
    ");
  }
}