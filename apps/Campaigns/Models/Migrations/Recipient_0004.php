<?php

namespace Hubleto\App\Community\Campaigns\Models\Migrations;

use Hubleto\Framework\Migration;

class Recipient_0004 extends Migration
{

  public function upgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipients` add `phone_number` varchar(255)');
    $this->db->execute('alter table `campaigns_recipients` add index `phone_number` (`phone_number`)');
  }

  public function downgradeSchema(): void
  {
    $this->db->execute('alter table `campaigns_recipients` drop index `phone_number`');
  }

  public function upgradeForeignKeys(): void
  {
    //
  }

  public function downgradeForeignKeys(): void
  {
    //
  }
}