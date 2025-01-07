<?php

namespace HubletoApp\Support;

class Loader extends \HubletoCore\Core\Module
{


  public function __construct(\HubletoCore $app)
  {
    parent::__construct($app);
  }

  public function init(): void
  {
    $this->app->router->httpGet([
      '/^support\/?$/' => Controllers\Dashboard::class,
    ]);

    // $this->app->sidebar->addLink(1, 98100, 'support', $this->translate('Support'), 'fas fa-circle-question');
  }

}