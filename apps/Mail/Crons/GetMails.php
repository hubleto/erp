<?php

namespace Hubleto\App\Community\Mail\Crons;

use Hubleto\App\Community\Mail\Mailer;

class GetMails extends \Hubleto\Erp\Cron
{
  public string $schedulingPattern = '* * * * *';

  public function compileEmailAddresses(array $input): string
  {
    $addresses = [];

    foreach ($input as $item) {
      $addresses[] = $item->mailbox . '@' . $item->host;
    }

    return join(', ', $addresses);
  }

  public function run(): void
  {
    /** @var Mailer */
    $mailer = $this->getService(Mailer::class);
    $mailer->getMails();
  }

}
