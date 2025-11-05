<?php

namespace Hubleto\App\Community\Worksheets;

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
      '/^worksheets\/?$/' => Controllers\Activities::class,
      '/^worksheets\/add\/?$/' => ['controller' => Controllers\Activities::class, 'vars' => ['recordId' => -1]],
      // '/^worksheets\/activities\/?$/' => Controllers\Activities::class,
      '/^worksheets\/activity-types\/?$/' => Controllers\ActivityTypes::class,

      '/^worksheets\/api\/daily-activity-chart\/?$/' => Controllers\Api\DailyActivityChart::class,
    ]);

    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'worksheets', $this->translate('Worksheets'), 'fas fa-user-clock');
    $appMenu->addItem($this, 'worksheets/activity-types', $this->translate('Activity types'), 'fas fa-table');
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\ActivityType::class)->dropTableIfExists()->install();
      $this->getModel(Models\Activity::class)->dropTableIfExists()->install();
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // Create any demo data to promote your app.
  }

}
