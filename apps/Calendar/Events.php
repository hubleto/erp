<?php

namespace HubletoApp\Community\Calendar;

class Events extends \Hubleto\Framework\Core
{

  public function loadRemindersSummary(int $idUser = 0): array
  {
    /** @var Controllers\Api\GetCalendarEvents */
    $getCalendarEvents = $this->getController(Controllers\Api\GetCalendarEvents::class);

    $remindersToday = $getCalendarEvents->loadEventsFromMultipleCalendars(
      date("Y-m-d", strtotime("-1 year")),
      date("Y-m-d"),
      ['completed' => false, 'idUser' => $idUser]
    );

    $dateTomorrow = date("Y-m-d", time() + 24 * 3600);
    $remindersTomorrow = $getCalendarEvents->loadEventsFromMultipleCalendars(
      $dateTomorrow,
      $dateTomorrow,
      ['completed' => false, 'idUser' => $idUser]
    );

    $dateLaterStart = date("Y-m-d", time() + 24 * 3600 * 2);
    $dateLaterEnd = date("Y-m-d", time() + 24 * 3600 * 7);
    $remindersLater = $getCalendarEvents->loadEventsFromMultipleCalendars(
      $dateLaterStart,
      $dateLaterEnd,
      ['completed' => false, 'idUser' => $idUser]
    );

    return [$remindersToday, $remindersTomorrow, $remindersLater];
  }

}