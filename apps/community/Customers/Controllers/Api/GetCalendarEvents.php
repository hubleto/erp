<?php

namespace HubletoApp\Community\Customers\Controllers\Api;

class GetCalendarEvents extends \HubletoApp\Community\Calendar\Controllers\Api\GetCalendarEvents {
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): array
  {
    return (array) $this->calendarManager
      ->getCalendar('customers')
      ->loadEvents($this->dateStart, $this->dateEnd)
    ;

  }
}