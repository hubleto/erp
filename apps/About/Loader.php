<?php

namespace Hubleto\App\Community\About;

class Loader extends \Hubleto\Framework\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  /**
   * Inits the app: adds routes, settings, calendars, hooks, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();
    $this->getRouter()->httpGet([ '/^about\/?$/' => Controllers\About::class ]);
  }

}
