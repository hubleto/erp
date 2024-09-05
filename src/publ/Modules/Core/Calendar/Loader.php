<?php

namespace CeremonyCrmApp\Modules\Core\Calendar;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function addRouting(\CeremonyCrmApp\Core\Router $router)
  {
    $router->addRoutingGroup(
      'calendar',
      'CeremonyCrmApp/Modules/Core/Calendar/Controllers',
      'CeremonyCrmApp/Modules/Core/Calendar/Views',
      [
        'idAccount' => '$1',
      ],
      [
        '' => 'Calendar',
      ]
    );
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 20100, 'calendar', $this->app->translate('Calendar'), 'fas fa-calendar');
  }

  public function generateTestData()
  {}
}