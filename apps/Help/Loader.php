<?php

namespace CeremonyCrmMod\Help;

class Loader extends \CeremonyCrmApp\Core\Module
{

  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^help\/?$/' => Controllers\Help::class,
    ]);

    // $this->app->sidebar->addLink(1, 1900, 'help', $this->translate('Help'), 'fas fa-life-ring', str_starts_with($this->app->requestedUri, 'help'));
  }

}