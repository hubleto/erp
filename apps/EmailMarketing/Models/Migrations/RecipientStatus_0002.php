<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class RecipientStatus_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `email_marketing_recipient_statuses` add `id_campaign` int(8)');
    $this->db->execute('alter table `email_marketing_recipient_statuses` add index(`id_campaign`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_recipient_statuses` drop `id_campaign`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_recipient_statuses` ADD CONSTRAINT `fk__email_marketing_recipient_statuses__id_campaign` FOREIGN KEY (`id_campaign`)
      REFERENCES `email_marketing_campaigns` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `email_marketing_recipient_statuses` DROP FOREIGN KEY `fk__email_marketing_recipient_statuses__id_campaign`;");
  }

}