<?php

namespace HubletoApp\Community\Desktop;

class Loader extends \HubletoMain\Core\App
{

  const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

  public SidebarManager $sidebar;
  public DashboardManager $dashboardManager;

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
    $this->sidebar = new SidebarManager($main);
    $this->dashboardManager = new DashboardManager($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^$/' => Controllers\Home::class,
    ]);

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->main->apps->community('Help')->addContextHelpUrls('/^\/?$/', [
      'en' => '',
    ]);
  }

  public function installDefaultPermissions(): void
  {
    $mPermission = new \HubletoApp\Community\Settings\Models\Permission($this->main);
    $permissions = [
      "HubletoApp/Community/Desktop/Home",
      "HubletoApp/Community/Desktop/Controllers/Home",
    ];

    foreach ($permissions as $permission) {
      $mPermission->eloquent->create([
        "permission" => $permission
      ]);
    }
  }
}