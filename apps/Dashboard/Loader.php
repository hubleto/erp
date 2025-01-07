<?php

namespace HubletoApp\Dashboard;

class Loader extends \HubletoCore\Core\Module
{


  public function __construct(\HubletoCore $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^$/' => Controllers\Home::class,
    ]);

    $this->app->sidebar->addLink(1, 0, '', $this->translate('Home'), 'fas fa-home', $this->app->requestedUri == '');
  }}