<?php

namespace Hubleto\App\Community\Usage\Extendibles;

class Tools extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'title' => $this->app->translate('Usage log'),
        'icon' => 'fas fa-chart-bar',
        'url' => 'usage/log',
      ]
    ];
  }

}