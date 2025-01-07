<?php

namespace CeremonyCrmMod\Deals\Controllers\Api;

class GetCalendarEvents extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): array
  {
    return $this->app->getCalendar(\CeremonyCrmMod\Deals\Calendar::class)->loadEvents($this->app->params);
  }
}