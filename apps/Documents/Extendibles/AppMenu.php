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
        'icon' => 'fas fa-arrow-down-1-9',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/reviews',
        'title' => $this->app->translate('Reviews'),
        'icon' => 'fas fa-spell-check',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/templates',
        'title' => $this->app->translate('Templates'),
        'icon' => 'fas fa-file',
      ],
      [
        'app' => $this->app,
        'url' => 'documents/files/browse',
        'title' => $this->app->translate('File manager'),
        'icon' => 'fas fa-table',
      ],
    ];
  }

}