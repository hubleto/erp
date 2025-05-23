<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controllers\Controller {
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): array
  {
    $dateStart = '';
    $dateEnd = '';

    if ($this->main->isUrlParam("start") && $this->main->isUrlParam("end")) {
      $dateStart = date("Y-m-d H:i:s", (int) strtotime($this->main->urlParamAsString("start")));
      $dateEnd = date("Y-m-d H:i:s", (int) strtotime($this->main->urlParamAsString("end")));
    } else {
      $dateStart = date("Y-m-d H:i:s");
      $dateEnd = date("Y-m-d H:i:s", strtotime("+1 day"));
    }

    return $this->loadEventsFromAllCalendars($dateStart, $dateEnd);
  }

  public function loadEventsFromAllCalendars(string $dateStart, string $dateEnd): array
  {

    $events = [];

    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;

    foreach ($calendarManager->getCalendars() as $calendarClass => $calendar) {
      $calEvents = (array) $calendar->loadEvents($dateStart, $dateEnd);
      foreach ($calEvents as $key => $value) {
        // $calEvents[$key]['SOURCECLASS'] = $calendarClass;
        $calEvents[$key]['SOURCEFORM'] = $calendar->activitySelectorConfig["formComponent"] ?? null;
        $calEvents[$key]['icon'] = $calendar->activitySelectorConfig["icon"] ?? null;
        // $calEvents[$key]['SOURCEFORM_MODALPROPS'] = $calendar->activitySelectorConfig["formModalProps"] ?? null;
      }
      $events = array_merge($events, $calEvents);
    }

    // $events = array_merge($events, $this->loadEvents($dateStart, $dateEnd));

    return $events;
  }
}