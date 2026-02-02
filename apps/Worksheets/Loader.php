<?php

namespace Hubleto\App\Community\Worksheets;

class Loader extends \Hubleto\Framework\App
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
      '/^worksheets\/?$/' => Controllers\Activities::class,
      '/^worksheets(\/(?<recordId>\d+))?\/?$/' => Controllers\Activities::class,
      '/^worksheets\/add\/?$/' => ['controller' => Controllers\Activities::class, 'vars' => ['recordId' => -1]],

      '/^worksheets\/activity-types\/?$/' => Controllers\ActivityTypes::class,

      '/^worksheets\/boards\/daily-chart\/?$/' => Controllers\Boards\DailyChart::class,
      '/^worksheets\/boards\/monthly-summary\/?$/' => Controllers\Boards\MonthlySummary::class,
      '/^worksheets\/api\/daily-activity-chart\/?$/' => Controllers\Api\DailyActivityChart::class,
    ]);

    /** @var \Hubleto\App\Community\Dashboards\Manager $dashboardsApp */
    $dashboardsApp = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    if ($dashboardsApp) {
      $dashboardsApp->addBoard(
        $this,
        $this->translate('Daily chart'),
        'worksheets/boards/daily-chart'
      );
      $dashboardsApp->addBoard(
        $this,
        $this->translate('Monthly summary'),
        'worksheets/boards/monthly-summary'
      );
    }

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
