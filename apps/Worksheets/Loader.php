<?php

namespace HubletoApp\Community\Worksheets;

class Loader extends \Hubleto\Framework\App
{
  // Uncomment following if you want a button for app's settings
  // to be rendered next in sidebar, right next to your app's button.
  // public bool $hasCustomSettings = true;

  // init
  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^worksheets\/?$/' => Controllers\Home::class,
      '/^worksheets\/activities\/?$/' => Controllers\Activities::class,
      '/^worksheets\/activity-types\/?$/' => Controllers\ActivityTypes::class,

      '/^worksheets\/api\/daily-activity-chart\/?$/' => Controllers\Api\DailyActivityChart::class,
    ]);

    $appMenu = $this->main->apps->community('Desktop')->appMenu;
    $appMenu->addItem($this, 'worksheets', $this->translate('Worksheets'), 'fas fa-user-clock');
    $appMenu->addItem($this, 'worksheets/activity-types', $this->translate('Activity types'), 'fas fa-table');
  }

  // installTables
  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\ActivityType($this->main))->dropTableIfExists()->install();
      (new Models\Activity($this->main))->dropTableIfExists()->install();
    }
    if ($round == 2) {
      // do something in the 2nd round, if required
    }
    if ($round == 3) {
      // do something in the 3rd round, if required
    }
  }

  // generateDemoData
  public function generateDemoData(): void
  {
    // Create any demo data to promote your app.
  }

}
