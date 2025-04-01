<?php

namespace HubletoApp\Community\Customers\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): array
  {
    return (array) $this->main
      ->calendarManager->getCalendar(\HubletoApp\Community\Customers\Calendar::class)
      ->loadEvents()
    ;
  }
}