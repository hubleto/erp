<?php

namespace HubletoApp\Community\Support;

class Loader extends \HubletoMain\Core\App
{


  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    $this->main->router->httpGet([
      '/^support\/?$/' => Controllers\Dashboard::class,
    ]);

    // $this->main->sidebar->addLink(1, 98100, 'support', $this->translate('Support'), 'fas fa-circle-question');
  }

}