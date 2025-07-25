<?php

namespace HubletoMain;

class Cron
{
  // CRON-formatted string specifying the scheduling pattern
  public string $schedulingPattern = '* * * * *';

  public \Hubleto\Framework\Loader $main;

  public function __construct(\Hubleto\Framework\Loader $main)
  {
    $this->main = $main;
  }

  public function run(): void
  {
    // to be overriden
  }

}
