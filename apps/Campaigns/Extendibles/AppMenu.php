<?php

namespace Hubleto\App\Community\Campaigns\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'campaigns',
        'title' => $this->app->translate('Campaigns'),
        'icon' => 'fas fa-users-viewfinder',
      ],
      [
        'app' => $this->app,
        'url' => 'campaigns/recipients',
        'title' => $this->app->translate('Recipients'),
        'icon' => 'fas fa-users',
      ],
    ];
  }

}