<?php

namespace HubletoApp\Community\Desktop;

class Loader extends \HubletoMain\Core\App
{

  const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

  public bool $canBeDisabled = false;
  public bool $permittedForAllUsers = true;

  public SidebarManager $sidebar;
  public AppMenuManager $appMenu;
  public DashboardManager $dashboard;

  public function __construct(\HubletoMain $main)
  {
    parent::__construct($main);
    $this->sidebar = new SidebarManager($main);
    $this->appMenu = new AppMenuManager($main);
    $this->dashboard = new DashboardManager($main);
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