<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class Recipient_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `email_marketing_recipients` add `id_campaign` int(8)');
    $this->db->execute('alter table `email_marketing_recipients` add index(`id_campaign`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_recipients` drop `id_campaign`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_recipients` ADD CONSTRAINT `fk__email_marketing_recipients__id_campaign` FOREIGN KEY (`id_campaign`)
      REFERENCES `email_marketing_campaigns` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `email_marketing_recipients` DROP FOREIGN KEY `fk__email_marketing_recipients__id_campaign`;");
  }

}