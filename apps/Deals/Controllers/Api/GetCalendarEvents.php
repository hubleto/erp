<?php

namespace HubletoApp\Deals\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {

  public function renderJson(): array
  {
    return $this->main->getCalendar(\HubletoApp\Deals\Calendar::class)->loadEvents($this->main->params);
  }
}