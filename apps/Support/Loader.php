<?php

namespace Hubleto\App\Community\Support;

class Loader extends \Hubleto\Framework\App
{
  
  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
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
