<?php

namespace HubletoApp\Community\Notifications\Crons;

class DailyDigest extends \HubletoMain\Cron
{
  public string $schedulingPattern = '05 06 * * *';

  public function run(): void
  {
    $emailsSent = [];
    $users = $this->getAuthProvider()->getActiveUsers();
    foreach ($users as $user) {

      /** @var \HubletoApp\Community\Notifications\Digest $digest */
      $digest = $this->getService(\HubletoApp\Community\Notifications\Digest::class);
      $digestHtml = $digest->getDailyDigestForUser($user);

      if (!empty($digestHtml)) {
        if ($this->getEmailProvider()->send($user['email'], 'Hubleto: Your Daily Digest', $digestHtml)) {
          $emailsSent[] = $user['email'];
        }
      }
    }

    $this->getLogger()->info('Daily digest sent to: ' . join(', ', $emailsSent));
  }

}
