<?php

namespace HubletoApp\Community\Dashboards;

class Loader extends \Hubleto\Framework\App
{
  // public bool $hasCustomSettings = true;

  protected array $boards = [];

  public function __construct(\HubletoMain\Loader $main)
  {
    parent::__construct($main);
  }

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^dashboards(\/(?<dashboardSlug>[^\/]+))?\/?$/' => Controllers\Dashboards::class,
      '/^settings\/dashboards\/?$/' => Controllers\Settings::class,
    ]);

    $this->main->apps->community('Settings')?->addSetting($this, [
      'title' => $this->translate('Dashboards'),
      'icon' => 'fas fa-table',
      'url' => 'settings/dashboards',
    ]);

  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Dashboard($this->main))->dropTableIfExists()->install();
      (new Models\Panel($this->main))->dropTableIfExists()->install();
    }
  }

  public function getBoards(): array
  {
    return $this->boards;
  }

  public function addBoard(\Hubleto\Framework\App $app, string $title, string $boardUrlSlug): void
  {
    $this->boards[$boardUrlSlug] = [
      'app' => $app,
      'title' => $title,
      'boardUrlSlug' => $boardUrlSlug,
    ];
  }

  public function generateDemoData(): void
  {
    $mDashboard = $this->main->di->create(Models\Dashboard::class);
    $mPanel = $this->main->di->create(Models\Panel::class);

    $dashboard = $mDashboard->record->recordCreate([
      'id_owner' => 1,
      'title' => 'Default dashboard',
      'slug' => 'default',
      'is_default' => true,
    ]);
    foreach ($this->boards as $board) {
      $mPanel->record->recordCreate([
        'id_dashboard' => $dashboard['id'],
        'title' => $board['title'],
        'board_url_slug' => $board['boardUrlSlug'],
        'configuration' => '',
      ]);
    }
  }

}
