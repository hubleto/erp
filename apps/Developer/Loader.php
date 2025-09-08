<?php

namespace Hubleto\App\Community\Developer;

class Loader extends \Hubleto\Framework\App
{

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
      '/^developer\/?$/' => Controllers\Dashboard::class,
      '/^developer\/db-updates\/?$/' => Controllers\DbUpdates::class,
      '/^developer\/form-designer\/?$/' => Controllers\FormDesigner::class,
    ]);

    // $tools = $this->getService(\Hubleto\App\Community\Tools\Manager::class);
    // $tools->addTool($this, [
    //   'title' => $this->translate('Developer tools'),
    //   'icon' => 'fas fa-screwdriver-wrench',
    //   'url' => 'developer',
    // ]);

  }

}
