<?php

namespace HubletoApp\Leads\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {

  public function renderJson(): array
  {
    return $this->main->getCalendar(\HubletoApp\Leads\Calendar::class)->loadEvents($this->main->params);
  }
}