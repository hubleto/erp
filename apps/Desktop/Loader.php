<?php

namespace Hubleto\App\Community\Desktop;

use Hubleto\App\Community\Settings\PermissionsManager;
use Hubleto\Erp\App;

class Loader extends \Hubleto\Erp\App
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
      '/^desktop\/api\/get-sidebar-badge-numbers\/?$/' => Controllers\Api\GetSidebarBadgeNumbers::class,
    ]);

    $sidebarGroups = $this->getSidebarGroups();
    foreach ($sidebarGroups as $key => $group) {
      $this->router()->get([
        '/^~\/' . $key . '$/' => ['controller' => $group['controller'] ?? Controllers\SidebarGroup::class, 'vars' => ['group' => $key]],
      ]);
    }

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->appMenu = $this->collectExtendibles('AppMenu');
  }

  /**
   * [Description for getSidebarGroups]
   *
   * @return [type]
   * 
   */
  public function getSidebarGroups() {
    return $this->config()->getAsArray('sidebarGroups', [
      'crm' => [ 'color' => '#7a23dc', 'title' => $this->translate('CRM'), 'icon' => 'fas fa-id-card-clip' ],
      'customer-acquisition' => [ 'color' => '#c6aa39', 'title' => $this->translate('Marketing'), 'icon' => 'fas fa-bullseye' ],
      'sales' => [ 'color' => '#f50ab9', 'title' => $this->translate('Sales'), 'icon' => 'fas fa-users-viewfinder' ],
      'productivity' => [ 'color' => '#20689f', 'title' => $this->translate('Productivity'), 'icon' => 'fas fa-diagram-project' ],
      'finance' => [ 'color' => '#ce4715ff', 'title' => $this->translate('Finance'), 'icon' => 'fas fa-credit-card' ],
      'custom' => [ 'color' => '#888888', 'title' => $this->translate('Custom'), 'icon' => 'fas fa-puzzle-piece' ],
      'maintenance' => [ 'color' => '#c0c90e', 'title' => $this->translate('Maintenance'), 'icon' => 'fas fa-cog' ],
      'help' => [ 'color' => '#005a16ff', 'title' => $this->translate('Help'), 'icon' => 'fas fa-life-ring' ],
    ]);
  }

  /**
   * [Description for getAppsInSidebar]
   *
   * @return array
   * 
   */
  public function getAppsInSidebar(): array
  {
    $appsInSidebar = $this->appManager()->getEnabledApps();

    foreach ($appsInSidebar as $appNamespace => $app) {
      if (
        !$this->getService(PermissionsManager::class)->isAppPermittedForActiveUser($app)
        || $app->configAsInteger('sidebarOrder') <= 0
      ) {
        unset($appsInSidebar[$appNamespace]);
      }
    }

    uasort($appsInSidebar, function ($a, $b) {
      $aOrder = $a->configAsInteger('sidebarOrder');
      $bOrder = $b->configAsInteger('sidebarOrder');
      return $aOrder <=> $bOrder;
    });

    return $appsInSidebar;
  }

  /**
   * [Description for getActivatedApp]
   *
   * @return App|null
   * 
   */
  public function getActivatedApp(): App|null
  {
    $activatedApp = null;

    foreach ($this->getAppsInSidebar() as $app) {
      if ($app->isActivated) {
        $activatedApp = $app;
      }
    }

    if ($activatedApp === null) {
      $activatedApp = $this->appManager()->getApp(\Hubleto\App\Community\Desktop\Loader::class);
    }

    return $activatedApp;

  }

}
