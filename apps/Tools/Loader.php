<?php

namespace Hubleto\App\Community\Tools;

class Loader extends \Hubleto\Framework\App
{
  public bool $canBeDisabled = false;

  public array $tools = [];

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();
    $this->getRouter()->httpGet([
      '/^tools\/?$/' => Controllers\Dashboard::class,
    ]);

    $this->tools = $this->collectExtendibles('Tools');
  }

}
