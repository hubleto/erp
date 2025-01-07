<?php

namespace HubletoApp\Deals\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {

  public function renderJson(): array
  {
    return $this->app->getCalendar(\HubletoApp\Deals\Calendar::class)->loadEvents($this->app->params);
  }
}