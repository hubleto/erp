<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controllers\Controller {
  public int $returnType = \ADIOS\Core\Controller::RETURN_TYPE_JSON;

  public function renderJson(): array
  {

    if ($this->main->isUrlParam("start") && $this->main->isUrlParam("end")) {
      $dateStart = date("Y-m-d H:i:s", (int) strtotime($this->main->urlParamAsString("start")));
      $dateEnd = date("Y-m-d H:i:s", (int) strtotime($this->main->urlParamAsString("end")));
    } else {
      $dateStart = date("Y-m-d H:i:s");
      $dateEnd = date("Y-m-d H:i:s", strtotime("+1 day"));
    }

    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;

    return (array) $calendarManager
      ->getCalendar('deals')
      ->loadEvents($dateStart, $dateEnd)
    ;

  }
}