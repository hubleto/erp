<?php

namespace HubletoApp\Community\Leads\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {

  public function renderJson(): array
  {
    return (array) $this->main
      ->calendarManager->getCalendar(\HubletoApp\Community\Leads\Calendar::class)
      ->loadEvents()
    ;
  }
}