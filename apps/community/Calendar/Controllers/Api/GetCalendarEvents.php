<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {

  public function renderJson(): array
  {
    $events = [];

    foreach ($this->main->calendarManager->getCalendars() as $calendarClass => $calendar) {
      $calEvents = (array) $calendar->loadEvents();
      foreach ($calEvents as $key => $value) {
        $calEvents[$key]['SOURCE'] = $calendarClass;
      }
      $events = array_merge($events, $calEvents);
    }

    return $events;
  }
}