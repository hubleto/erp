<?php

namespace CeremonyCrmMod\Core\Calendar;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^calendar\/?$/' => Controllers\Calendar::class,
      '/^calendar\/get-calendar-events\/?$/' => Controllers\Api\GetCalendarEvents::class,
    ]);

    $this->app->sidebar->addLink(1, 50, 'calendar', $this->translate('Calendar'), 'fas fa-calendar-days', str_starts_with($this->app->requestedUri, 'calendar'));
  }

}