<?php

namespace Hubleto\App\Community\EmailMarketing\Models\Migrations;

use Hubleto\Framework\Migration;

class CampaignScheduleRecipient_0002 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `email_marketing_campaigns_schedule_recipients` add `id_mail` int(8)');
    $this->db->execute('alter table `email_marketing_campaigns_schedule_recipients` add index(`id_mail`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute("alter table `email_marketing_campaigns_schedule_recipients` drop `id_mail`");
  }

  public function upgradeForeignKeys(): void
  {
    $this->db->execute("
      ALTER TABLE `email_marketing_campaigns_schedule_recipients` ADD CONSTRAINT `fk_email_marketing_campaigns_schedule_recipients__id_mail` FOREIGN KEY (`id_mail`)
      REFERENCES `mails` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
    ");
  }

  public function downgradeForeignKeys(): void
  {
    $this->db->execute("ALTER TABLE `email_marketing_recipients` DROP FOREIGN KEY `fk_email_marketing_campaigns_schedule_recipients__id_mail`;");
  }

}