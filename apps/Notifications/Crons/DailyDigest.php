<?php

namespace Hubleto\App\Community\Notifications\Crons;

class DailyDigest extends \Hubleto\Erp\Cron
{
  public string $schedulingPattern = '05 06 * * *';

  public function run(): void
  {
    $emailsSent = [];
    $users = $this->authProvider()->getActiveUsers();
    foreach ($users as $user) {

      /** @var \Hubleto\App\Community\Notifications\Digest $digest */
      $digest = $this->getService(\Hubleto\App\Community\Notifications\Digest::class);
      $digestHtml = $digest->getDailyDigestForUser($user);

      if (!empty($digestHtml)) {
        if ($this->emailProvider()->send($user['email'], 'Hubleto: Your Daily Digest', $digestHtml)) {
          $emailsSent[] = $user['email'];
        }
      }
    }

    $this->logger()->info('Daily digest sent to: ' . join(', ', $emailsSent));
  }

}
