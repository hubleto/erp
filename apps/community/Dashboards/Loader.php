<?php

namespace HubletoApp\Community\Dashboards;

class Loader extends \HubletoMain\Core\App
{

  // public bool $hasCustomSettings = true;

  protected array $boards = [];

  public function __construct(\HubletoMain $main)
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

  public function addBoard(\HubletoMain\Core\App $app, string $title, string $boardUrlSlug): void
  {
    $this->boards[$boardUrlSlug] = [
      'app' => $app,
      'title' => $title,
      'boardUrlSlug' => $boardUrlSlug,
    ];
  }

  public function generateDemoData(): void
  {
    $mDashboard = new Models\Dashboard($this->main);
    $mPanel = new Models\Panel($this->main);

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