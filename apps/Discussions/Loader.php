<?php

namespace Hubleto\App\Community\Discussions;

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
      '/^discussions(\/(?<recordId>\d+))?\/?$/' => Controllers\Discussions::class,
      '/^discussions\/members(\/(?<recordId>\d+))?\/?$/' => Controllers\Members::class,
      '/^discussions\/messages(\/(?<recordId>\d+))?\/?$/' => Controllers\Messages::class,
      '/^discussions\/api\/send-message\/?$/' => Controllers\Api\SendMessage::class,
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
