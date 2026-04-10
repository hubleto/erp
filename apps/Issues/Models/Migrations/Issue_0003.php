<?php

namespace Hubleto\App\Community\Issues\Models\Migrations;

use Hubleto\Framework\Migration;

class Issue_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `issues` add `thread_uid` varchar(255)");
    $this->db->execute("alter table `issues` add index(`thread_uid`)");

    $this->db->execute("alter table `issues` add index(`id_customer`)");
    $this->db->execute("alter table `issues` add index(`id_mail`)");
    $this->db->execute("alter table `issues` add index(`id_workflow`)");
    $this->db->execute("alter table `issues` add index(`id_workflow_step`)");
    $this->db->execute("alter table `issues` add index(`id_owner`)");
    $this->db->execute("alter table `issues` add index(`id_manager`)");
    $this->db->execute("alter table `issues` add index(`is_closed`)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `issues` drop `thread_uid`");
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}