<?php

namespace Hubleto\App\Community\Dashboards;

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
      '/^dashboards\/api\/sort-panels\/?$/' => Controllers\Api\SortPanels::class,
      '/^dashboards\/api\/set-panel-width\/?$/' => Controllers\Api\SetPanelWidth::class,

      '/^dashboards(\/(?<dashboardSlug>[^\/]+))?\/?$/' => Controllers\Dashboards::class,
      '/^dashboards\/manage?\/?$/' => Controllers\DashboardsManage::class,
    ]);

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    // $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    // $settingsApp->addSetting($this, [
    //   'title' => $this->translate('Dashboards'),
    //   'icon' => 'fas fa-table',
    //   'url' => 'dashboards/manage',
    // ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Dashboard::class)->dropTableIfExists()->install();
      $this->getModel(Models\Panel::class)->dropTableIfExists()->install();
    }
  }

  public function generateDemoData(): void
  {
    $mDashboard = $this->getModel(Models\Dashboard::class);
    $mPanel = $this->getModel(Models\Panel::class);

    $dashboard = $mDashboard->record->recordCreate([
      'id_owner' => 1,
      'title' => 'Default dashboard',
      'slug' => 'default',
      'is_default' => true,
    ]);

    $boards = $this->getService(Manager::class);
    foreach ($boards->getBoards() as $board) {
      $mPanel->record->recordCreate([
        'id_dashboard' => $dashboard['id'],
        'title' => $board['title'],
        'board_url_slug' => $board['boardUrlSlug'],
        'configuration' => '',
        'width' => rand(2, 3),
      ]);
    }
  }

  public function renderSecondSidebar(): string
  {
    return '
      <div class="flex flex-col gap-2">
        <a class="btn btn-square btn-primary-outline" href="' . $this->env()->projectUrl . '/dashboards">
          <span class="icon"><i class="fas fa-table"></i></span>
          <span class="text">' . $this->translate('Dashboards') . '</span>
        </a>
        <a class="btn btn-transparent" href="' . $this->env()->projectUrl . '/dashboards/manage">
          <span class="icon"><i class="fas fa-list"></i></span>
          <span class="text">' . $this->translate('Manage') . '</span>
        </a>
      </div>
    ';
  }

}
