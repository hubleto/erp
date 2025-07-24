<?php

/*
  This cron does not do anything. It is just an example of how the
  cron should be implemented.
*/

namespace HubletoMain\Cron;

class Example extends \Hubleto\Framework\Cron
{

  // CRON-formatted string specifying the scheduling pattern
  public string $schedulingPattern = '*/10 * * * *';

  public function run(): void
  {
    $this->main->logger->info("Sample cron started.");
  }

}