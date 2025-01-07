<?php

namespace HubletoApp\Calendar\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {

  public function renderJson(): array
  {
    $events = [];

    foreach ($this->app->getCalendars() as $calendarClass => $calendar) {
      $calEvents = $calendar->loadEvents($this->app->params);
      foreach ($calEvents as $key => $value) {
        $calEvents[$key]['sourceCalendar'] = $calendarClass;
      }
      $events = array_merge($events, $calEvents);
    }

    return $events;
  }
}