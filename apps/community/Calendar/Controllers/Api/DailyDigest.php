<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;

class DailyDigest extends \HubletoMain\Core\Controllers\ApiController
{

  public function formatReminder(string $date, array $reminder): array {
    $text = $date . ': ' . $reminder['title'] . '; ' . $reminder['details'];
    $url = 'calendar?eventSource=' . $reminder['source'] . '&eventId=' . $reminder['id'];

    return ['text' => $text, 'url' => $url];
  }

  public function response(): array
  {
    $digest = [];

    list($remindersToday, $remindersTomorrow, $remindersLater) = $this->hubletoApp->loadRemindersSummary($this->user['id'] ?? 0);

    foreach ($remindersToday as $reminder) $digest[] = $this->formatReminder('Today', $reminder);
    foreach ($remindersTomorrow as $reminder) $digest[] = $this->formatReminder('Tomorrow', $reminder);
    foreach ($remindersLater as $reminder) $digest[] = $this->formatReminder('Later', $reminder);

    return $digest;
  }

}