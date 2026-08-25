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

  /**
   * [Description for renderSecondSidebar]
   *
   * @return string
   * 
   */
  public function renderSecondSidebar(): string
  {
    /** @var Models\Dashboard */
    $mDashboard = $this->getModel(Models\Dashboard::class);

    $dashboards = $mDashboard->record->prepareReadQuery()
      ->with('PANELS')
      ->orderBy('is_default')
      ->get()
    ;

    $dashboardButtonsHtml = '<div class="list dense">';
    foreach ($dashboards as $dashboard) {
      $dashboardButtonsHtml .= '
        <a
          class="
            btn btn-list-item
            ' . ($dashboard->slug == $this->router()->urlParamAsString('dashboardSlug') ? "btn-active" : "btn-transparent") . '
          "
          href="' . $this->env()->projectUrl . '/dashboards/~/' . $dashboard->slug . '"
        >
          <span class="text">' . $dashboard->title . '</span>
        </a>
      ';
    }
    $dashboardButtonsHtml .= '</div>';

    return '
      ' . $this->secondSidebarTitle() . '
      <div class="app-sidebar-buttons">
        ' . $dashboardButtonsHtml . '
      </div>
    ';
  }

  public function getWelcomeScreenMessages(): array
  {
    $messages = [];

    /** @var Models\Dashboard */
    $mDashboard = $this->getModel(Models\Dashboard::class);

    $defaultDashboard = $mDashboard->getDefaultDashboard();

    if (!$defaultDashboard) {
      $messages[] = [
        'class' => 'warning',
        'icon' => 'fas fa-table',
        'title' => 'No dashboard configured',
        'content' => '
          You do not have default dashboard configured.
        ',
        'button' => '
          <a class="btn btn-primary" href="dashboards">
            <span class="text">Configure your default dashboard</span>
          </a>
        ',
      ];
    }

    return $messages;
  }

}
