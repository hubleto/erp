<?php

namespace CeremonyCrmMod\Core\Extensions;

class Loader extends \CeremonyCrmApp\Core\Module
{


  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^extensions\/?$/' => Controllers\Dashboard::class,
    ]);

    $this->app->sidebar->addLink(1, 999999, 'extensions', $this->translate('Extensions'), 'fas fa-puzzle-piece');
  }

}