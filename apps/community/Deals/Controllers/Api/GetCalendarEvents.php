<?php

namespace HubletoApp\Community\Deals\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {

  public function renderJson(): array
  {
    return $this->main->getCalendar(\HubletoApp\Community\Deals\Calendar::class)->loadEvents($this->main->getUrlParams());
  }
}