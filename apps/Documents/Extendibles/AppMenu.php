<?php

namespace HubletoApp\Community\Documents\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'documents/browse',
        'title' => $this->app->translate('Browse'),
        'icon' => 'fas fa-table',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/list',
        'title' => $this->app->translate('List'),
        'icon' => 'fas fa-list',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/templates',
        'title' => $this->app->translate('Templates'),
        'icon' => 'fas fa-list',
      ],
    ];
  }

}