<?php

namespace Hubleto\App\Community\Discussions;

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
      '/^discussions\/api\/send-message\/?$/' => Controllers\Api\SendMessage::class,

      '/^discussions(\/(?<recordId>\d+))?\/?$/' => Controllers\Discussions::class,
      '/^discussions\/add?\/?$/' => ['controller' => Controllers\Discussions::class, 'vars' => [ 'recordId' => -1 ]],
      '/^discussions\/members(\/(?<recordId>\d+))?\/?$/' => Controllers\Members::class,
      '/^discussions\/members\/add?\/?$/' => ['controller' => Controllers\Members::class, 'vars' => [ 'recordId' => -1 ]],
      '/^discussions\/messages(\/(?<recordId>\d+))?\/?$/' => Controllers\Messages::class,
      '/^discussions\/messages\/add?\/?$/' => ['controller' => Controllers\Messages::class, 'vars' => [ 'recordId' => -1 ]],
    ]);

  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Discussion::class)->dropTableIfExists()->install();
      $this->getModel(Models\Member::class)->dropTableIfExists()->install();
      $this->getModel(Models\Message::class)->dropTableIfExists()->install();
    }
  }

}
