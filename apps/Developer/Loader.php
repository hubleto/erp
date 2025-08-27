<?php

namespace HubletoApp\Community\Developer;

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

    $this->getRouter()->httpGet([
      '/^developer\/?$/' => Controllers\Dashboard::class,
      '/^developer\/db-updates\/?$/' => Controllers\DbUpdates::class,
      '/^developer\/form-designer\/?$/' => Controllers\FormDesigner::class,
    ]);

    // $tools = $this->getService(\HubletoApp\Community\Tools\Manager::class);
    // $tools->addTool($this, [
    //   'title' => $this->translate('Developer tools'),
    //   'icon' => 'fas fa-screwdriver-wrench',
    //   'url' => 'developer',
    // ]);

  }

}
