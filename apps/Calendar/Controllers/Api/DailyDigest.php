<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;

class DailyDigest extends \HubletoMain\Controllers\ApiController
{
  public function formatReminder(string $category, string $color, array $reminder): array
  {
    return [
      'color' => $color,
      'category' => $category,
      'text' => $reminder['title'],
      'url' => 'calendar?eventSource=' . $reminder['source'] . '&eventId=' . $reminder['id'],
      'description' => $reminder['details'],
    ];
  }

  public function response(): array
  {
    $digest = [];

    list($remindersToday, $remindersTomorrow, $remindersLater) = $this->hubletoApp->loadRemindersSummary($this->user['id'] ?? 0);

    foreach ($remindersToday as $reminder) {
      $digest[] = $this->formatReminder('Today', '#EED202', $reminder);
    }
    foreach ($remindersTomorrow as $reminder) {
      $digest[] = $this->formatReminder('Tomorrow', '#92DFF3', $reminder);
    }
    foreach ($remindersLater as $reminder) {
      $digest[] = $this->formatReminder('Later', '#92DFF3', $reminder);
    }

    return $digest;
  }

}
