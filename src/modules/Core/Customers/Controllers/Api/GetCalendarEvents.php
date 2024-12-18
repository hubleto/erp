<?php

namespace CeremonyCrmMod\Core\Customers\Controllers\Api;

class GetCalendarEvents extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): array
  {
    return $this->app->getCalendar(\CeremonyCrmMod\Core\Customers\Calendar::class)->loadEvents($this->app->params);
  }
}