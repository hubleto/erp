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
    // $this->sidebar = DependencyInjection::create(SidebarManager::class);
    // $this->dashboard = DependencyInjection::create(DashboardManager::class);
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
      'crm' => [ 'color' => '#7a23dc', 'title' => $this->translate('CRM'), 'icon' => 'fas fa-id-card-clip' ],
      'marketing' => [ 'color' => '#c6aa39', 'title' => $this->translate('Marketing'), 'icon' => 'fas fa-bullseye' ],
      'sales' => [ 'color' => '#f50ab9', 'title' => $this->translate('Sales'), 'icon' => 'fas fa-users-viewfinder' ],
      'productivity' => [ 'color' => '#20689f', 'title' => $this->translate('Productivity'), 'icon' => 'fas fa-diagram-project' ],
      'e-commerce' => [ 'color' => '#700065ff', 'title' => $this->translate('E-Commerce'), 'icon' => 'fas fa-cart-shopping' ],
      'supply-chain' => [ 'color' => '#4bbd44', 'title' => $this->translate('Supply chain'), 'icon' => 'fas fa-truck' ],
      'finance' => [ 'color' => '#ce4715ff', 'title' => $this->translate('Finance'), 'icon' => 'fas fa-credit-card' ],
      'custom' => [ 'color' => '#888888', 'title' => $this->translate('Custom'), 'icon' => 'fas fa-puzzle-piece' ],
      'maintenance' => [ 'color' => '#c0c90e', 'title' => $this->translate('Maintenance'), 'icon' => 'fas fa-cog' ],
      'help' => [ 'color' => '#005a16ff', 'title' => $this->translate('Help'), 'icon' => 'fas fa-life-ring' ],

      'addressbook' => [ 'color' => '#888888', 'title' => $this->translate('Addressbook'), 'icon' => 'fas fa-id-card-clip' ],
      'calendar' => [ 'color' => '#888888', 'title' => $this->translate('Calendar'), 'icon' => 'fas fa-calendar' ],
      'documents' => [ 'color' => '#888888', 'title' => $this->translate('Documents'), 'icon' => 'fas fa-file' ],
      'communication' => [ 'color' => '#888888', 'title' => $this->translate('Communication'), 'icon' => 'fas fa-comments' ],
      'workflow' => [ 'color' => '#888888', 'title' => $this->translate('Workflow'), 'icon' => 'fas fa-diagram-project' ],
      'helpdesk' => [ 'color' => '#888888', 'title' => $this->translate('Helpdesk'), 'icon' => 'fas fa-headset' ],
      'events' => [ 'color' => '#888888', 'title' => $this->translate('Events'), 'icon' => 'fas fa-people-group' ],
      'website' => [ 'color' => '#888888', 'title' => $this->translate('Website'), 'icon' => 'fas fa-globe' ],
      'reporting' => [ 'color' => '#888888', 'title' => $this->translate('Reporting'), 'icon' => 'fas fa-chart-line' ],
    ]);
}

}
