<?php

namespace Hubleto\App\Community\Worksheets;

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

    $this->router()->crud('worksheets', Controllers\Activities::class);
    $this->router()->crud('worksheets/activity-types', Controllers\ActivityTypes::class);

    $this->router()->get([
      '/^worksheets\/boards\/daily-chart\/?$/' => Controllers\Boards\DailyChart::class,
      '/^worksheets\/boards\/monthly-summary\/?$/' => Controllers\Boards\MonthlySummary::class,
      '/^worksheets\/boards\/quota\/?$/' => Controllers\Boards\Quota::class,
      '/^worksheets\/boards\/work-table\/?$/' => Controllers\Boards\WorkTable::class,
      '/^worksheets\/boards\/hours-by-month\/?$/' => Controllers\Boards\HoursByMonth::class,
      '/^worksheets\/boards\/my-tasks\/?$/' => Controllers\Boards\MyTasks::class,
      '/^worksheets\/boards\/pinned-tasks\/?$/' => Controllers\Boards\PinnedTask::class,
      '/^worksheets\/boards\/latest-tasks\/?$/' => Controllers\Boards\LatestWorkedTasks::class,
      '/^worksheets\/api\/daily-activity-chart\/?$/' => Controllers\Api\DailyActivityChart::class,
    ]);

    /** @var \Hubleto\App\Community\Dashboards\Manager $dashboardsApp */
    $dashboardsApp = $this->getService(\Hubleto\App\Community\Dashboards\Manager::class);
    if ($dashboardsApp) {
      $dashboardsApp->addBoard(
        $this,
        $this->translate('Daily chart'),
        'worksheets/boards/daily-chart',
        Controllers\Boards\DailyChart::class,
      );
      $dashboardsApp->addBoard(
        $this,
        $this->translate('Monthly summary'),
        'worksheets/boards/monthly-summary',
        Controllers\Boards\MonthlySummary::class,
      );

      $dashboardsApp->addBoard(
        $this,
        $this->translate('Quota for today'),
        'worksheets/boards/quota'
      );
      $dashboardsApp->addBoard(
        $this,
        $this->translate('Work Table'),
        'worksheets/boards/work-table'
      );
      $dashboardsApp->addBoard(
        $this,
        $this->translate('Hours by Month'),
        'worksheets/boards/hours-by-month'
      );
      $dashboardsApp->addBoard(
        $this,
        $this->translate('My Tasks'),
        'worksheets/boards/my-tasks'
      );
      $dashboardsApp->addBoard(
        $this,
        $this->translate('Pinned tasks'),
        'worksheets/boards/pinned-tasks'
      );
      $dashboardsApp->addBoard(
        $this,
        $this->translate('Latest worked on tasks'),
        'worksheets/boards/latest-tasks'
      );
    }

    $appMenu = $this->getService(\Hubleto\App\Community\Desktop\AppMenuManager::class);
    $appMenu->addItem($this, 'worksheets', $this->translate('Worksheets'), 'fas fa-user-clock');
    $appMenu->addItem($this, 'worksheets/activity-types', $this->translate('Activity types'), 'fas fa-table');
  }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   *
   */
  public function renderSecondSidebar(): string
  {
    return '
      ' . $this->secondSidebarTitle() .'
      <div class="app-sidebar-buttons">
        ' . $this->secondSidebarButton('worksheets/activity-types', 'fas fa-tag', 'Activity types') . '
      </div>
    ';
  }

  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\ActivityType::class)->upgradeSchema();
      $this->getModel(Models\Activity::class)->upgradeSchema();
    }
  }

}
