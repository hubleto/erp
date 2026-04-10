<?php

namespace Hubleto\App\Community\Issues\Models\Migrations;

use Hubleto\Framework\Migration;

class Issue_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute("alter table `issues` change `problem` `description` text");

    $this->db->execute("alter table `issues` add `notes` text");
    $this->db->execute("alter table `issues` add `id_customer` int(8)");
    $this->db->execute("alter table `issues` add `id_mail` int(8)");
    $this->db->execute("alter table `issues` add `id_workflow` int(8)");
    $this->db->execute("alter table `issues` add `id_workflow_step` int(8)");
    $this->db->execute("alter table `issues` add `id_owner` int(8)");
    $this->db->execute("alter table `issues` add `id_manager` int(8)");
    $this->db->execute("alter table `issues` add `shared_with` json");
    $this->db->execute("alter table `issues` add `is_closed` int(1)");
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `issues` change `description` `problem` text");

    $this->db->execute("alter table `issues` drop `notes`");
    $this->db->execute("alter table `issues` drop `id_customer`");
    $this->db->execute("alter table `issues` drop `id_mail`");
    $this->db->execute("alter table `issues` drop `id_workflow`");
    $this->db->execute("alter table `issues` drop `id_workflow_step`");
    $this->db->execute("alter table `issues` drop `id_owner`");
    $this->db->execute("alter table `issues` drop `id_manager`");
    $this->db->execute("alter table `issues` drop `shared_with`");
    $this->db->execute("alter table `issues` drop `is_closed`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `issues` ADD CONSTRAINT `fk__issues__id_customer` FOREIGN KEY (`id_customer`) REFERENCES `customers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
      ALTER TABLE `issues` ADD CONSTRAINT `fk__issues__id_mail` FOREIGN KEY (`id_mail`) REFERENCES `mails` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
      ALTER TABLE `issues` ADD CONSTRAINT `fk__issues__id_workflow` FOREIGN KEY (`id_workflow`) REFERENCES `workflows` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
      ALTER TABLE `issues` ADD CONSTRAINT `fk__issues__id_workflow_step` FOREIGN KEY (`id_workflow_step`) REFERENCES `workflow_steps` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
      ALTER TABLE `issues` ADD CONSTRAINT `fk__issues__id_owner` FOREIGN KEY (`id_owner`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
      ALTER TABLE `issues` ADD CONSTRAINT `fk__issues__id_manager` FOREIGN KEY (`id_manager`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `issues` DROP FOREIGN KEY `fk__issues__id_issue`;
      ALTER TABLE `issues` DROP FOREIGN KEY `fk__issues__id_customer`;
      ALTER TABLE `issues` DROP FOREIGN KEY `fk__issues__id_mail`;
      ALTER TABLE `issues` DROP FOREIGN KEY `fk__issues__id_workflow`;
      ALTER TABLE `issues` DROP FOREIGN KEY `fk__issues__id_workflow_step`;
      ALTER TABLE `issues` DROP FOREIGN KEY `fk__issues__id_owner`;
      ALTER TABLE `issues` DROP FOREIGN KEY `fk__issues__id_manager`;
    ");
  }
}