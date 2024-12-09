<?php

namespace CeremonyCrmApp\Modules\Core\Calendar;

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

    // $router(
    //   'calendar',
    //   'CeremonyCrmApp/Modules/Core/Calendar/Controllers',
    //   '@app/Modules/Core/Calendar/Views',
    //   [
    //     'idAccount' => '$1',
    //   ],
    //   [
    //     '' => 'Calendar',
    //   ]
    // );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 20100, 'calendar', $this->app->translate('Calendar'), 'fas fa-calendar');
  }

}