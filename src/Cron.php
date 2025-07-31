<?php declare(strict_types=1);

namespace HubletoMain;

class Cron
{
  // CRON-formatted string specifying the scheduling pattern
  public string $schedulingPattern = '* * * * *';

  public function __construct(public \Hubleto\Framework\Loader $main)
  {
  }

  public function run(): void
  {
    // to be overriden
  }

}
