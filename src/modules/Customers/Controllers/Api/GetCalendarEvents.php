<?php

namespace CeremonyCrmMod\Customers\Controllers\Api;

class GetCalendarEvents extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): array
  {
    return $this->app->getCalendar(\CeremonyCrmMod\Customers\Calendar::class)->loadEvents($this->app->params);
  }
}