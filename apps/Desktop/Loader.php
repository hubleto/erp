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

    $this->router()->get([
      '/^$/' => Controllers\Home::class,
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

  public function getSidebarGroups() {
    return $this->config()->getAsArray('sidebarGroups', [
      'addressbook' => [ 'title' => $this->translate('Addressbook'), 'icon' => 'fas fa-id-card-clip' ],
      'calendar' => [ 'title' => $this->translate('Calendar'), 'icon' => 'fas fa-calendar' ],
      // 'productivity' => [ 'title' => $this->translate('Productivity'), 'icon' => 'fas fa-list-check' ],
      'documents' => [ 'title' => $this->translate('Documents'), 'icon' => 'fas fa-file' ],
      'communication' => [ 'title' => $this->translate('Communication'), 'icon' => 'fas fa-comments' ],
      'workflow' => [ 'title' => $this->translate('Workflow'), 'icon' => 'fas fa-diagram-project' ],
      'marketing' => [ 'title' => $this->translate('Marketing'), 'icon' => 'fas fa-bullseye' ],
      'sales' => [ 'title' => $this->translate('Sales'), 'icon' => 'fas fa-users-viewfinder' ],
      'projects' => [ 'title' => $this->translate('Projects'), 'icon' => 'fas fa-diagram-project' ],
      'supply-chain' => [ 'title' => $this->translate('Supply chain'), 'icon' => 'fas fa-truck' ],
      'helpdesk' => [ 'title' => $this->translate('Helpdesk'), 'icon' => 'fas fa-headset' ],
      'events' => [ 'title' => $this->translate('Events'), 'icon' => 'fas fa-people-group' ],
      'e-commerce' => [ 'title' => $this->translate('E-Commerce'), 'icon' => 'fas fa-cart-shopping' ],
      'website' => [ 'title' => $this->translate('Website'), 'icon' => 'fas fa-globe' ],
      'finance' => [ 'title' => $this->translate('Finance'), 'icon' => 'fas fa-credit-card' ],
      'reporting' => [ 'title' => $this->translate('Reporting'), 'icon' => 'fas fa-chart-line' ],
      'maintenance' => [ 'title' => $this->translate('Maintenance'), 'icon' => 'fas fa-cog' ],
      'help' => [ 'title' => $this->translate('Help'), 'icon' => 'fas fa-life-ring' ],
      'custom' => [ 'title' => $this->translate('Custom'), 'icon' => 'fas fa-puzzle-piece' ],
    ]);
}

}
