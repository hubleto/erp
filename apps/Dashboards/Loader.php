<?php

namespace Hubleto\App\Community\Dashboards;

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
      '/^dashboards\/api\/save-panel-order\/?$/' => Controllers\Api\SavePanelOrder::class,
      '/^dashboards\/api\/set-panel-width\/?$/' => Controllers\Api\SetPanelWidth::class,

      '/^dashboards(\/(?<dashboardSlug>[^\/]+))?\/?$/' => Controllers\Dashboards::class,
      '/^dashboards\/manage\/?$/' => Controllers\Settings::class,
    ]);

    /** @var \Hubleto\App\Community\Settings\Loader $settingsApp */
    $settingsApp = $this->appManager()->getApp(\Hubleto\App\Community\Settings\Loader::class);
    $settingsApp->addSetting($this, [
      'title' => $this->translate('Dashboards'),
      'icon' => 'fas fa-table',
      'url' => 'dashboards/manage',
    ]);

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
      ]);
    }
  }

}
