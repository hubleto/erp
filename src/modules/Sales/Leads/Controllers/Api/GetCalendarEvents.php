<?php

namespace CeremonyCrmMod\Sales\Leads\Controllers\Api;

class GetCalendarEvents extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): array
  {
    return $this->app->getCalendar(\CeremonyCrmMod\Sales\Leads\Calendar::class)->loadEvents($this->app->params);
  }
}