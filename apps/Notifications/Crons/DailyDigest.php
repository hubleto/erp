<?php

namespace HubletoApp\Community\Notifications\Crons;

class DailyDigest extends \HubletoMain\Cron
{
  public string $schedulingPattern = '05 06 * * *';

  public function run(): void
  {
    $emailsSent = [];
    $users = $this->main->auth->getActiveUsers();
    foreach ($users as $user) {
      $digestHtml = $this->main->apps->community('Notifications')->getDailyDigestForUser($user);
      if (!empty($digestHtml)) {
        if ($this->main->email->send($user['email'], 'Hubleto: Your Daily Digest', $digestHtml)) {
          $emailsSent[] = $user['email'];
        }
      }
    }

    $this->main->logger->info('Daily digest sent to: ' . join(', ', $emailsSent));
  }

}
