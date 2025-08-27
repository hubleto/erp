<?php

namespace Hubleto\App\Community\Help;

class Loader extends \Hubleto\Framework\App
{
  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public array $contextHelp = [];

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
      '/^help\/?$/' => Controllers\Help::class,
      '/^help\/search\/?$/' => Controllers\Search::class,
    ]);

    $this->contextHelp = $this->collectExtendibles('ContextHelp');
  }

}
