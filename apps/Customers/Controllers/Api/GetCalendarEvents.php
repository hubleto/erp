<?php

namespace HubletoApp\Customers\Controllers\Api;

class GetCalendarEvents extends \HubletoMain\Core\Controller {

  public function renderJson(): array
  {
    return $this->app->getCalendar(\HubletoApp\Customers\Calendar::class)->loadEvents($this->app->params);
  }
}