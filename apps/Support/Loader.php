<?php

namespace Hubleto\App\Community\Support;

class Loader extends \Hubleto\Erp\App
{
  
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
      '/^support\/?$/' => Controllers\Dashboard::class,
    ]);

  }

}
