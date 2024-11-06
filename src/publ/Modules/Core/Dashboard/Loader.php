<?php

namespace CeremonyCrmApp\Modules\Core\Dashboard;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function addRouting(\CeremonyCrmApp\Core\Router $router)
  {
    $router->addRouting([
      '/^$/' => [
        'controller' => 'CeremonyCrmApp/Modules/Core/Dashboard/Controllers/Home',
        'view' => '@app/Modules/Core/Dashboard/Views/Home',
      ]
    ]);
  }


  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 0, '', $this->app->translate('Home'), 'fas fa-home');
  }}