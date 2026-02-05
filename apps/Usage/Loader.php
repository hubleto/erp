<?php

namespace Hubleto\App\Community\Usage;

use Hubleto\Framework\EventListener;

class Loader extends \Hubleto\Erp\App
{
  public const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

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
      '/^usage\/?$/' => Controllers\Home::class,
      '/^usage\/log\/?$/' => Controllers\Log::class,
    ]);

    $this->eventManager()->addEventListener(
      'onControllerBeforeInit',
      $this->getService(EventListeners\LogUsage::class)
    );
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Log::class)->dropTableIfExists()->install();
    }
  }

}
