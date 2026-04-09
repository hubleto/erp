<?php

namespace Hubleto\App\Community\Calendar;

class Events extends \Hubleto\Erp\Core
{

  public function loadRemindersSummary(int $idUser = 0): array
  {
    /** @var Controllers\Api\GetCalendarEvents */
    $getCalendarEvents = $this->getController(Controllers\Api\GetCalendarEvents::class);

    $remindersToday = $getCalendarEvents->loadEventsFromMultipleCalendars(
      date("Y-m-d", strtotime("-1 year")),
      date("Y-m-d"),
      ['fCompleted' => 1, 'idUser' => $idUser]
    );

    $dateTomorrow = date("Y-m-d", time() + 24 * 3600);
    $remindersTomorrow = $getCalendarEvents->loadEventsFromMultipleCalendars(
      $dateTomorrow,
      $dateTomorrow,
      ['fCompleted' => 1, 'idUser' => $idUser]
    );

    $dateLaterStart = date("Y-m-d", time() + 24 * 3600 * 2);
    $dateLaterEnd = date("Y-m-d", time() + 24 * 3600 * 7);
    $remindersLater = $getCalendarEvents->loadEventsFromMultipleCalendars(
      $dateLaterStart,
      $dateLaterEnd,
      ['fCompleted' => 1, 'idUser' => $idUser]
    );

    return [$remindersToday, $remindersTomorrow, $remindersLater];
  }

}