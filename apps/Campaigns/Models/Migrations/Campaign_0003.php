<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Campaign_0003 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns` add `mail_subject` varchar(255)');
    $this->db->execute('alter table `campaigns` add `mail_body` text');

    $this->db->execute('update `campaigns` set `mail_subject` = (select `subject` from `mails_templates` where `mails_templates`.`id` = `campaigns`.`id_mail_template`)');
    $this->db->execute('update `campaigns` set `mail_body` = (select `body_html` from `mails_templates` where `mails_templates`.`id` = `campaigns`.`id_mail_template`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns` drop `mail_subject`');
    $this->db->execute('alter table `campaigns` drop `mail_body`');
  }

  public function upgradeForeignKeys(): void
  {
  }

  public function downgradeForeignKeys(): void
  {
  }
}