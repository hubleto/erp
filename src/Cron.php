<?php declare(strict_types=1);

namespace HubletoMain;

class Cron extends \Hubleto\Framework\CoreClass
{
  // CRON-formatted string specifying the scheduling pattern
  public string $schedulingPattern = '* * * * *';

  public function run(): void
  {
    // to be overriden
  }

}
