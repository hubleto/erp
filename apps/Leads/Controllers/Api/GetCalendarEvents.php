<?php

namespace HubletoApp\Leads\Controllers\Api;

class GetCalendarEvents extends \HubletoCore\Core\Controller {

  public function renderJson(): array
  {
    return $this->app->getCalendar(\HubletoApp\Leads\Calendar::class)->loadEvents($this->app->params);
  }
}