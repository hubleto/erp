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
    ]);

    $this->app->sidebar->addLink(1, 20100, 'calendar', $this->translate('Calendar'), 'fas fa-calendar');
  }

}