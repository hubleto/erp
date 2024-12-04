<?php

namespace CeremonyCrmApp\Modules\Core\Support;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^support\/?$/' => Controllers\Dashboard::class,
    ]);

    // $router(
    //   '/^support$/' => [
    //     'controller' => 'CeremonyCrmApp/Modules/Core/Support/Controllers/Dashboard',
    //     'view' => '@app/Modules/Core/Support/Views/Dashboard',
    //   ]
    // ]);
  }

  public function modifySidebar(\CeremonyCrmApp\Core\Sidebar $sidebar)
  {
    $sidebar->addLink(1, 98100, 'support', $this->app->translate('Support'), 'fas fa-circle-question');
  }

}