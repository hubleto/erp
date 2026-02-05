<?php

namespace Hubleto\App\Community\Tools;

class Loader extends \Hubleto\Erp\App
{
  public bool $canBeDisabled = false;

  public array $tools = [];

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();
    $this->router()->get([
      '/^tools\/?$/' => Controllers\Dashboard::class,
    ]);

    $this->tools = $this->collectExtendibles('Tools');
  }

}
