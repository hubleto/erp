<?php

namespace CeremonyCrmApp\Modules\Core\Support;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function addRouting(\CeremonyCrmApp\Core\Router $router)
  {
    $router->addRouting([
      '/^support$/' => [
        'controller' => 'CeremonyCrmApp/Modules/Core/Support/Controllers/Dashboard',
        'view' => 'CeremonyCrmApp/Modules/Core/Support/Views/Dashboard',
      ]
    ]);
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 98100, 'support', $this->app->translate('Support'), 'fas fa-question');
  }

}