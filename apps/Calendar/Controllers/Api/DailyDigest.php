<?php

namespace Hubleto\App\Community\Calendar\Controllers\Api;

class DailyDigest extends \Hubleto\Erp\Controllers\ApiController
{
  public function formatReminder(string $category, string $color, array $reminder): array
  {
    return [
      'color' => $color,
      'category' => $category,
      'text' => $reminder['title'],
      'url' => empty($reminder['url']) ? 'calendar?showActivity=' . $reminder['source'] . ',' . $reminder['id'] : $reminder['url'],
      'description' => $reminder['details'],
    ];
  }

  public function response(): array
  {
    $digest = [];

    $events = $this->getService(\Hubleto\App\Community\Calendar\Events::class);
    list($remindersToday, $remindersTomorrow, $remindersLater) = $events->loadRemindersSummary($this->user['id'] ?? 0);

    foreach ($remindersToday as $reminder) {
      $digest[] = $this->formatReminder($this->translate('Today'), '#EED202', $reminder);
    }
    foreach ($remindersTomorrow as $reminder) {
      $digest[] = $this->formatReminder($this->translate('Tomorrow'), '#92DFF3', $reminder);
    }
    foreach ($remindersLater as $reminder) {
      $digest[] = $this->formatReminder($this->translate('Later'), '#92DFF3', $reminder);
    }

    return $digest;
  }

}
