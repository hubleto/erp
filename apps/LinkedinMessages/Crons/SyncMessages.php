<?php

namespace Hubleto\App\Custom\LinkedinMessages\Crons;

use Hubleto\App\Custom\LinkedinMessages\Sync;

class SyncMessages extends \Hubleto\Erp\Cron
{
  public string $schedulingPattern = '*/15 * * * *';

  public function run(): void
  {
    /** @var Sync */
    $sync = $this->getService(Sync::class);
    $sync->syncAll();
  }

}
