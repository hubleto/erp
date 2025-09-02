<?php

namespace Hubleto\App\Community\Pipeline\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'pipeline/history',
        'title' => $this->app->translate('History'),
        'icon' => 'fas fa-envelope',
      ],
    ];
  }

}