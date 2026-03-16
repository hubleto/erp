<?php

namespace Hubleto\App\Community\Settings\Extendibles;

class Tools extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'title' => $this->app->translate('Log viewer'),
        'icon' => 'fas fa-screwdriver-wrench',
        'url' => 'settings/log-viewer',
      ]
    ];
  }

}