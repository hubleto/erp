<?php

namespace Hubleto\App\Community\Documents\Extendibles;

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
        'title' => $this->app->translate('Show as list'),
        'icon' => 'fas fa-list',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/folders',
        'title' => $this->app->translate('Manage folders'),
        'icon' => 'fas fa-folder',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/templates',
        'title' => $this->app->translate('Templates'),
        'icon' => 'fas fa-file',
      ],
    ];
  }

}