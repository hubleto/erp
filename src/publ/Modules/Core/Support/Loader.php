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

    $this->app->sidebar->addLink(1, 98100, 'support', $this->app->translate('Support'), 'fas fa-circle-question');
  }

}