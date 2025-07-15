<?php

namespace HubletoApp\Community\Calendar\Controllers\Api;


use ADIOS\Core\Controller;

class ShareCalendar extends \HubletoMain\Core\Controllers\Controller
{

  public int $returnType = Controller::RETURN_TYPE_JSON;

  public function renderJson(): ?array
  {
    if (!isset($this->app->getUrlParams()['calendar'])) {
      return [];
    }

    $calendar = $this->app->getUrlParams()['calendar'];
    $calendarManager = $this->main->apps->community('Calendar')->calendarManager;

    var_dump($calendarManager->getCalendar($calendar)->calendarConfig);

    $calendarManager->getCalendar($calendar)->calendarConfig['shared'] = true;

    return $calendarManager->getCalendars();
  }

}