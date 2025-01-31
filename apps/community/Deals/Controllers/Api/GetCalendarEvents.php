<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {

  public function renderJson(): array
  {
    return (array) $this->main
      ->calendarManager->getCalendar(\HubletoApp\Community\Deals\Calendar::class)
      ->loadEvents()
    ;
  }
}