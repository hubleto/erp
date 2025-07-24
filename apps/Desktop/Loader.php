<?php

namespace HubletoApp\Community\Desktop;

class Loader extends \Hubleto\Framework\App
{
  public const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public SidebarManager $sidebar;
  public AppMenuManager $appMenu;
  public DashboardManager $dashboard;

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);
    $this->sidebar = $main->di->create(SidebarManager::class);
    $this->appMenu = $main->di->create(AppMenuManager::class);
    $this->dashboard = $main->di->create(DashboardManager::class);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^$/' => Controllers\Home::class,
    ]);

    $this->setConfigAsInteger('sidebarOrder', 0);

    $this->main->apps->community('Help')?->addContextHelpUrls('/^\/?$/', [
      'en' => '',
    ]);
  }

}
