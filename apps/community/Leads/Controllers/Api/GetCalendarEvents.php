<?php

namespace HubletoApp\Community\Leads\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {

  public function renderJson(): array
  {
    return $this->main->getCalendar(\HubletoApp\Community\Leads\Calendar::class)->loadEvents($this->main->params);
  }
}