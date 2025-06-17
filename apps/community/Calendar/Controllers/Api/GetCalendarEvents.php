<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controllers\Controller {
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public string $dateStart = '';
  public string $dateEnd = '';

  public \HubletoApp\Community\Calendar\CalendarManager $calendarManager;
  
  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);

    if ($this->main->apps->community('Calendar')) {
      $this->calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    }

    if ($this->main->isUrlParam("start") && $this->main->isUrlParam("end")) {
      $dateStart = date("Y-m-d H:i:s", (int) strtotime($this->main->urlParamAsString("start")));
      $dateEnd = date("Y-m-d H:i:s", (int) strtotime($this->main->urlParamAsString("end")));
    } else {
      $dateStart = date("Y-m-d H:i:s");
      $dateEnd = date("Y-m-d H:i:s", strtotime("+1 day"));
    }

    $this->dateStart = $dateStart;
    $this->dateEnd = $dateEnd;

  }

  public function renderJson(): array
  {
    if ($this->main->isUrlParam('source')) {
      return (array) $this->calendarManager
        ->getCalendar($this->main->urlParamAsString('source'))
        ->loadEvents($this->dateStart, $this->dateEnd)
      ;
    } else {
      return $this->loadEventsFromAllCalendars($this->dateStart, $this->dateEnd);
    }
  }

  public function loadEventsFromAllCalendars(string $dateStart, string $dateEnd, array $filter = []): array
  {

    $events = [];

    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;

    foreach ($calendarManager->getCalendars() as $calendarClass => $calendar) {
      $calEvents = (array) $calendar->loadEvents($dateStart, $dateEnd, $filter);
      foreach ($calEvents as $key => $value) {
        $calEvents[$key]['SOURCEFORM'] = $calendar->activitySelectorConfig["formComponent"] ?? null;
        $calEvents[$key]['icon'] = $calendar->activitySelectorConfig["icon"] ?? null;
      }
      $events = array_merge($events, $calEvents);
    }

    return $events;
  }
}