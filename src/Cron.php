<?php declare(strict_types=1);

namespace Hubleto\Erp;

class Cron extends \Hubleto\Erp\Core
{
  // CRON-formatted string specifying the scheduling pattern
  public string $schedulingPattern = '* * * * *';

  public function run(): void
  {
    // to be overriden
  }

}
