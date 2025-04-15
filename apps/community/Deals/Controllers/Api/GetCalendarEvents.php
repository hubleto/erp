<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): array
  {
    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;
    return (array) $calendarManager
      ->getCalendar(\HubletoApp\Community\Deals\Calendar::class)
      ->loadEvents()
    ;
  }
}