<?php

namespace Hubleto\App\Community\LinkedinMessages\Crons;

use Hubleto\App\Community\LinkedinMessages\Sync;

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
