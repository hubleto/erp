<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Controllers\ApiController
{
  public string $dateStart = '';
  public string $dateEnd = '';

  public \HubletoApp\Community\Calendar\CalendarManager $calendarManager;

  public function __construct(\HubletoMain\Loader $main)
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
    $filter = [
      'fOwnership' => $this->main->urlParamAsInteger('fOwnership'),
    ];

    if ($this->main->isUrlParam('source')) {
      $calendar = $this->calendarManager->getCalendar($this->main->urlParamAsString('source'));
      if ($this->main->isUrlParam('id')) {
        $event = (array) $calendar->loadEvent($this->main->urlParamAsInteger('id'));
        $event['SOURCEFORM'] = $calendar->calendarConfig["formComponent"] ?? null;

        return $event;

      } else {
        return $calendar->loadEvents($this->dateStart, $this->dateEnd, $filter);
      }
    } else {
      return $this->loadEventsFromMultipleCalendars(
        $this->dateStart,
        $this->dateEnd,
        $filter,
        $this->main->urlParamAsArray('fSources')
      );
    }
  }

  public function loadEventsFromMultipleCalendars(
    string $dateStart,
    string $dateEnd,
    array $filter = [],
    array|null $sources = null
  ): array {

    $events = [];

    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;

    foreach ($calendarManager->getCalendars() as $source => $calendar) {
      if ($sources !== null && !in_array($source, $sources)) {
        continue;
      }

      $calEvents = (array) $calendar->loadEvents($dateStart, $dateEnd, $filter);
      foreach ($calEvents as $key => $value) {
        $calEvents[$key]['SOURCEFORM'] = $calendar->calendarConfig["formComponent"] ?? null;
        $calEvents[$key]['icon'] = $calendar->calendarConfig["icon"] ?? null;
      }
      $events = array_merge($events, $calEvents);
    }

    return $events;
  }
}
