<?php

namespace Hubleto\App\Community\Api;

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
      '/^api\/?$/' => Controllers\Home::class,
      '/^api\/keys(\/(?<recordId>\d+))?\/?$/' => Controllers\Keys::class,
      '/^api\/keys\/add?\/?$/' => ['controller' => Controllers\Keys::class, 'vars' => [ 'recordId' => -1 ]],
      '/^api\/permissions(\/(?<recordId>\d+))?\/?$/' => Controllers\Permissions::class,
      '/^api\/permissions\/add?\/?$/' => ['controller' => Controllers\Permissions::class, 'vars' => [ 'recordId' => -1 ]],
      '/^api\/usages(\/(?<recordId>\d+))?\/?$/' => Controllers\Usages::class,
      '/^api\/usages\/add?\/?$/' => ['controller' => Controllers\Usages::class, 'vars' => [ 'recordId' => -1 ]],
    ]);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Key::class)->dropTableIfExists()->install();
      $this->getModel(Models\Permission::class)->dropTableIfExists()->install();
      $this->getModel(Models\Usage::class)->dropTableIfExists()->install();
    }
  }

}
