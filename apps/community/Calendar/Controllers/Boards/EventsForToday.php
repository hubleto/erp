<?php

namespace HubletoApp\Community\Calendar\Controllers\Boards;

class EventsForToday extends \HubletoMain\Core\Controller {

  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $getCalendarEvents = new \HubletoApp\Community\Calendar\Controllers\Api\GetCalendarEvents($this->main);

    $events = $getCalendarEvents->loadEventsFromAllCalendars(date("Y-m-d"), date("Y-m-d"));

    $this->viewParams['events'] = $events;
 
    $this->setView('@HubletoApp:Community:Calendar/Boards/EventsForToday.twig');
  }

}