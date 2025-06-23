<?php

namespace HubletoApp\Community\Calendar\Controllers\Boards;

class EventsForToday extends \HubletoMain\Core\Controllers\Controller {

  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $getCalendarEvents = new \HubletoApp\Community\Calendar\Controllers\Api\GetCalendarEvents($this->main);

    $eventsToday = $getCalendarEvents->loadEventsFromMultipleCalendars(date("Y-m-d"), date("Y-m-d"), ['completed' => false]);
    $this->viewParams['eventsToday'] = $eventsToday;
 
    $dateTomorrow = date("Y-m-d", time() + 24*3600);
    $eventsTomorrow = $getCalendarEvents->loadEventsFromMultipleCalendars($dateTomorrow, $dateTomorrow, ['completed' => false]);
    $this->viewParams['eventsTomorrow'] = $eventsTomorrow;
 
    $dateLaterStart = date("Y-m-d", time() + 24*3600 * 2);
    $dateLaterEnd = date("Y-m-d", time() + 24*3600 * 7);
    $eventsLater = $getCalendarEvents->loadEventsFromMultipleCalendars($dateLaterStart, $dateLaterEnd, ['completed' => false]);
    $this->viewParams['eventsLater'] = $eventsLater;
 
    $this->setView('@HubletoApp:Community:Calendar/Boards/EventsForToday.twig');
  }

}