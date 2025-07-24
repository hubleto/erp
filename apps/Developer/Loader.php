<?php

namespace HubletoApp\Community\Developer;

class Loader extends \Hubleto\Framework\App
{
  // init
  public function init(): void
  {
    parent::init();

    // Add app routes.
    // By default, each app should have a welcome dashboard.
    // If your app will have own settings panel, it should be under the `settings/your-app` slug.
    $this->main->router->httpGet([
      '/^developer\/?$/' => Controllers\Dashboard::class,
      '/^developer\/db-updates\/?$/' => Controllers\DbUpdates::class,
      '/^developer\/form-designer\/?$/' => Controllers\FormDesigner::class,
    ]);

    // Uncomment following to configure your app's menu
    // $appMenu = $this->main->apps->community('Desktop')->appMenu;
    // $appMenu->addItem($this, 'developer/item-1', $this->translate('Item 1'), 'fas fa-table');
    // $appMenu->addItem($this, 'developer/item-2', $this->translate('Item 2'), 'fas fa-list');


    if ($this->main->apps->community('Tools')) {
      $this->main->apps->community('Tools')->addTool($this, [
        'title' => $this->translate('Developer tools'),
        'icon' => 'fas fa-screwdriver-wrench',
        'url' => 'developer',
      ]);
    }

  }

}
