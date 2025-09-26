<?php

namespace Hubleto\App\Community\BkJournal;

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
      '/^journal\/entries\/?$/' => Controllers\Entries::class,
      '/^journal\/entries\/add\/?$/' => ['controller' => Controllers\Entries::class, 'vars' => ['recordId' => -1]],
    ]);
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Entry::class)->dropTableIfExists()->install();
    } else if ($round == 2) {
      $this->getModel(Models\EntryLine::class)->dropTableIfExists()->install();
    }
  }

}