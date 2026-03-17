<?php

namespace Hubleto\App\Community\Api\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'api',
        'title' => $this->app->translate('API'),
        'icon' => 'fas fa-arrow-right-arrow-left',
      ],
      [
        'app' => $this->app,
        'url' => 'api/keys',
        'title' => $this->app->translate('Keys'),
        'icon' => 'fas fa-key',
      ],
      [
        'app' => $this->app,
        'url' => 'api/usage',
        'title' => $this->app->translate('Usage log'),
        'icon' => 'fas fa-check-double',
      ],
    ];
  }

}