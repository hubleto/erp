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
        'title' => $this->app->translate('Document Browser'),
        'icon' => 'fas fa-table',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/list',
        'title' => $this->app->translate('Documents Table'),
        'icon' => 'fas fa-list',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/folders',
        'title' => $this->app->translate('Folders'),
        'icon' => 'fas fa-folder',
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