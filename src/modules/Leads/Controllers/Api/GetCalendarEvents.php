<?php

namespace CeremonyCrmMod\Leads\Controllers\Api;

class GetCalendarEvents extends \CeremonyCrmApp\Core\Controller {

  public function renderJson(): array
  {
    return $this->app->getCalendar(\CeremonyCrmMod\Leads\Calendar::class)->loadEvents($this->app->params);
  }
}