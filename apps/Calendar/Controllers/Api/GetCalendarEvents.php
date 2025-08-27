<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Controllers\ApiController
{
  public string $dateStart = '';
  public string $dateEnd = '';

  public function __construct()
  {
    parent::__construct();

    if ($this->getRouter()->isUrlParam("start") && $this->getRouter()->isUrlParam("end")) {
      $dateStart = date("Y-m-d H:i:s", (int) strtotime($this->getRouter()->urlParamAsString("start")));
      $dateEnd = date("Y-m-d H:i:s", (int) strtotime($this->getRouter()->urlParamAsString("end")));
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
      'fOwnership' => $this->getRouter()->urlParamAsInteger('fOwnership'),
    ];

    if ($this->getRouter()->isUrlParam('source')) {

      /** @var \HubletoApp\Community\Calendar\Manager $calendarManager */
      $calendarManager = $this->getService(\HubletoApp\Community\Calendar\Manager::class);

      $calendar = $calendarManager->getCalendar($this->getRouter()->urlParamAsString('source'));
      if ($this->getRouter()->isUrlParam('id')) {
        $event = (array) $calendar->loadEvent($this->getRouter()->urlParamAsInteger('id'));
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
        $this->getRouter()->urlParamAsArray('fSources')
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

    /** @var \HubletoApp\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\HubletoApp\Community\Calendar\Manager::class);

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
