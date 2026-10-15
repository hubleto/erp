<?php

namespace Hubleto\App\Community\About;

class Loader extends \Hubleto\Erp\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  /**
   * Inits the app: adds routes, settings, calendars, event listeners, menu items, ...
   *
   * @return void
   * 
   */
  public function init(): void
  {
    parent::init();
    $this->setConfigAsInteger('sidebarOrder', 0);
    $this->router()->get([ '/^about\/?$/' => Controllers\About::class ]);
  }

}
