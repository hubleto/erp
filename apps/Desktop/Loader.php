<?php

namespace Hubleto\App\Community\Desktop;

use Hubleto\App\Community\Desktop\Services\Sidebar;

class Loader extends \Hubleto\Erp\App
{
  public const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public SidebarManager $sidebar;
  public DashboardManager $dashboard;

  public function __construct()
  {
    parent::__construct();
    // $this->sidebar = DependencyInjection::create(SidebarManager::class);
    // $this->dashboard = DependencyInjection::create(DashboardManager::class);
  }

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
      '/^$/' => Controllers\Home::class,
      '/^desktop\/api\/set-sidebar-group-collapsed\/?$/' => Controllers\Api\SetSidebarGroupCollapsed::class,
      '/^desktop\/api\/update-sidebar-badge-numbers\/?$/' => Controllers\Api\UpdateSidebarBadgeNumbers::class,
    ]);

    /** @var Sidebar */
    $sidebarService = $this->getService(Sidebar::class);

    $sidebarGroups = $sidebarService->getSidebarGroups();
    foreach ($sidebarGroups as $key => $group) {
      $this->router()->get([
        '/^~\/' . $key . '$/' => ['controller' => $group['controller'] ?? Controllers\SidebarGroup::class, 'vars' => ['group' => $key]],
      ]);
    }

    $this->setConfigAsInteger('sidebarOrder', 0);

  }

}
