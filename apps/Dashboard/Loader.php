<?php

namespace HubletoApp\Dashboard;

class Loader extends \HubletoMain\Core\Module
{


  public function __construct(\HubletoMain $app)
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