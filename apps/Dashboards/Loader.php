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

      '/^dashboards\/~(\/(?<dashboardSlug>[^\/]+))?\/?$/' => Controllers\Dashboard::class,
      '/^dashboards(\/(?<recordId>\d+))?\/?$/' => Controllers\Dashboards::class,
      '/^dashboards\/add\/?$/' => ['controller' => Controllers\Dashboards::class, 'vars' => ['recordId' => -1]],
    ]);
  }

  /**
   * [Description for installApp]
   *
   * @param int $round
   * 
   * @return void
   * 
   */
  public function installApp(int $round): void
  {
    if ($round == 1) {
      $this->getModel(Models\Dashboard::class)->upgradeSchema();
      $this->getModel(Models\Panel::class)->upgradeSchema();
    }
  }

  // public function generateDemoData(): void
  // {
  //   $mDashboard = $this->getModel(Models\Dashboard::class);
  //   $mPanel = $this->getModel(Models\Panel::class);

  //   $dashboard = $mDashboard->record->recordCreate([
  //     'id_owner' => 1,
  //     'title' => $this->translate('My dashboard'),
  //     'slug' => 'default',
  //     'is_default' => true,
  //   ]);

  //   $boards = $this->getService(Manager::class);
  //   foreach ($boards->getBoards() as $board) {
  //     $mPanel->record->recordCreate([
  //       'id_dashboard' => $dashboard['id'],
  //       'title' => $board['title'],
  //       'board_url_slug' => $board['boardUrlSlug'],
  //       'configuration' => '',
  //       'width' => rand(2, 3),
  //     ]);
  //   }
  // }

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   * 
   */
  public function renderSecondSidebar(): string
  {
    return '
      <div class="app-main-title"><a href="' . $this->env()->projectUrl . '/dashboards">
        ' . $this->translate('Dashboards') . '
      </a></div>
      <div class="app-sidebar-buttons">
        <a class="btn btn-white" href="' . $this->env()->projectUrl . '/dashboards/manage">
          <span class="icon"><i class="fas fa-list"></i></span>
          <span class="text">' . $this->translate('Manage') . '</span>
        </a>
      </div>
    ';
  }

}
