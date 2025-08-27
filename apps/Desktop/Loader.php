<?php

namespace Hubleto\App\Community\Desktop;

use Hubleto\Framework\DependencyInjection;

class Loader extends \Hubleto\Framework\App
{
  public const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public array $appMenu = [];

  public SidebarManager $sidebar;
  public DashboardManager $dashboard;

  public function __construct()
  {
    parent::__construct();
    // $this->sidebar = DependencyInjection::create($main, SidebarManager::class);
    // $this->dashboard = DependencyInjection::create($main, DashboardManager::class);
    $this->sidebar = DependencyInjection::create(SidebarManager::class);
    $this->dashboard = DependencyInjection::create(DashboardManager::class);
  }

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
      '/^$/' => Controllers\Home::class,
    ]);

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->appMenu = $this->collectExtendibles('AppMenu');
  }

}
