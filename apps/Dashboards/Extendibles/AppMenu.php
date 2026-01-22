<?php

namespace Hubleto\App\Community\Dashboards\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'dashboards',
        'title' => $this->app->translate('Dashboards'),
        'icon' => 'fas fa-table',
      ],
      [
        'app' => $this->app,
        'url' => 'dashboards/manage',
        'title' => $this->app->translate('Manage'),
        'icon' => 'fas fa-list',
      ],
    ];
  }

}