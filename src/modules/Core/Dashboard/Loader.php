<?php

namespace CeremonyCrmMod\Core\Dashboard;

class Loader extends \CeremonyCrmApp\Core\Module
{


  public function __construct(\CeremonyCrmApp $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^$/' => Controllers\Home::class,
    ]);

    $this->app->sidebar->addLink(1, 0, '', $this->translate('Home'), 'fas fa-home');
  }}