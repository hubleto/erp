<?php

namespace HubletoApp\Community\Usage;

class Loader extends \HubletoMain\Core\App
{

  const DEFAULT_INSTALLATION_CONFIG = [
    'sidebarOrder' => 0,
  ];

  public function init(): void
  {
    parent::init();

    $this->main->router->httpGet([
      '/^usage\/?$/' => Controllers\Home::class,
      '/^usage\/log\/?$/' => Controllers\Log::class,
      '/^usage\/statistics\/?$/' => Controllers\Statistics::class,
    ]);

    $this->main->addSetting($this, [
      'title' => $this->translate('Usage log'),
      'icon' => 'fas fa-chart-bar',
      'url' => 'usage/log',
    ]);

    $this->setConfigAsInteger('sidebarOrder', 0);
  }

  public function logUsage(string $message = ''): void
  {
    if ((bool) $this->main->auth->getUserId()) {
      $urlParams = $this->main->getUrlParams();
      $mLog = new Models\Log($this->main);
      $mLog->record->recordCreate([
        'datetime' => date('Y-m-d H:i:s'),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
        'route' => trim($this->main->route, '/'),
        // 'params' => count($urlParams) == 0 ? '' : json_encode($urlParams),
        'message' => $message,
        'id_user' => $this->main->auth->getUserId(),
      ]);
    }
  }

  public function installTables(int $round): void
  {
    if ($round == 1) {
      (new Models\Log($this->main))->dropTableIfExists()->install();
    }
  }

}