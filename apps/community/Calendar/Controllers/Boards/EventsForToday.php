<?php

namespace HubletoApp\Community\Calendar\Controllers\Boards;

class EventsForToday extends \HubletoMain\Core\Controllers\Controller {

  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $getCalendarEvents = new \HubletoApp\Community\Calendar\Controllers\Api\GetCalendarEvents($this->main);

    $eventsToday = $getCalendarEvents->loadEventsFromAllCalendars(date("Y-m-d"), date("Y-m-d"), ['completed' => true]);
    $this->viewParams['eventsToday'] = $eventsToday;
 
    $dateTomorrow = date("Y-m-d", time() + 24*3600);
    $eventsTomorrow = $getCalendarEvents->loadEventsFromAllCalendars($dateTomorrow, $dateTomorrow, ['completed' => true]);
    $this->viewParams['eventsTomorrow'] = $eventsTomorrow;
 
    $dateLaterStart = date("Y-m-d", time() + 24*3600 * 2);
    $dateLaterEnd = date("Y-m-d", time() + 24*3600 * 7);
    $eventsLater = $getCalendarEvents->loadEventsFromAllCalendars($dateLaterStart, $dateLaterEnd, ['completed' => true]);
    $this->viewParams['eventsLater'] = $eventsLater;
 
    $this->setView('@HubletoApp:Community:Calendar/Boards/EventsForToday.twig');
  }

}