<?php

namespace Hubleto\App\Community\Usage;

class Loader extends \Hubleto\Framework\App
{
  public const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

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
      '/^usage\/?$/' => Controllers\Home::class,
      '/^usage\/log\/?$/' => Controllers\Log::class,
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Log::class)->dropTableIfExists()->install();
    }
  }

}
