<?php

namespace Hubleto\App\Community\Developer;

class Loader extends \Hubleto\Erp\App
{

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
      '/^developer\/?$/' => Controllers\Dashboard::class,
      '/^developer\/check-db-consistency\/?$/' => Controllers\CheckDbConsistency::class,
      '/^developer\/upgrade-models\/?$/' => Controllers\UpgradeModels::class,
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
