<?php

namespace CeremonyCrmMod\Sales\Deals\Controllers\Api;

class GetCalendarEvents extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): array
  {
    return $this->app->getCalendar(\CeremonyCrmMod\Sales\Deals\Calendar::class)->loadEvents($this->app->params);
  }
}