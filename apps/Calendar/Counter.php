<?php

namespace Hubleto\App\Community\Calendar;

use Hubleto\Erp\Core;

class Counter extends Core
{

  /**
   * [Description for missedIncompleteActivities]
   *
   * @return int
   * 
   */
  public function missedIncompleteActivities(): int
  {
    /** @var Controllers\Api\GetCalendarEvents */
    $getCalendarEvents = $this->getController(Controllers\Api\GetCalendarEvents::class);

    $missedActivities = $getCalendarEvents->loadEventsFromMultipleCalendars(
      "2000-01-01",
      date("Y-m-d"),
      ['fCompleted' => false, 'idUser' => $this->authProvider()->getUserId()]
    );

    return count($missedActivities);
  }

}
