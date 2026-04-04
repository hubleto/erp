<?php

namespace Hubleto\App\Community\Documents\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'documents',
        'title' => $this->app->translate('Documents'),
        'icon' => 'fas fa-file',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/versions',
        'title' => $this->app->translate('Versions'),
        'icon' => 'fas fa-file',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/files/browse',
        'title' => $this->app->translate('File browser'),
        'icon' => 'fas fa-table',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/files/list',
        'title' => $this->app->translate('Uploaded files'),
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