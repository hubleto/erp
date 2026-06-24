<?php

namespace Hubleto\App\Community\Deals\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'deals',
        'title' => $this->app->translate('Deals'),
        'icon' => 'fas fa-people-arrows',
      ],
      [
        'app' => $this->app,
        'url' => 'deals/plan',
        'title' => $this->app->translate('Plan'),
        'icon' => 'fas fa-list-ol',
      ],
      [
        'app' => $this->app,
        'url' => 'calendar?show=deals',
        'title' => $this->app->translate('Calendar'),
        'icon' => 'fas fa-calendar-days',
      ],
    ];
  }

}