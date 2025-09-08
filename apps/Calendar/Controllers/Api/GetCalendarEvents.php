<?php

namespace Hubleto\App\Community\Calendar\Controllers\Api;

class GetCalendarEvents extends \Hubleto\Erp\Controllers\ApiController
{
  public string $dateStart = '';
  public string $dateEnd = '';

  public function __construct()
  {
    parent::__construct();

    if ($this->router()->isUrlParam("start") && $this->router()->isUrlParam("end")) {
      $dateStart = date("Y-m-d H:i:s", (int) strtotime($this->router()->urlParamAsString("start")));
      $dateEnd = date("Y-m-d H:i:s", (int) strtotime($this->router()->urlParamAsString("end")));
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
      'fOwnership' => $this->router()->urlParamAsInteger('fOwnership'),
    ];

    if ($this->router()->isUrlParam('source')) {

      /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
      $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);

      $calendar = $calendarManager->getCalendar($this->router()->urlParamAsString('source'));
      if ($this->router()->isUrlParam('id')) {
        $event = (array) $calendar->loadEvent($this->router()->urlParamAsInteger('id'));
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
        $this->router()->urlParamAsArray('fSources')
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

    /** @var \Hubleto\App\Community\Calendar\Manager $calendarManager */
    $calendarManager = $this->getService(\Hubleto\App\Community\Calendar\Manager::class);

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
