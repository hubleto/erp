<?php

namespace Hubleto\App\Community\Leads\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'leads',
        'title' => $this->app->translate('Leads'),
        'icon' => 'fas fa-people-arrows',
      ],
      [
        'app' => $this->app,
        'url' => 'leads/plan',
        'title' => $this->app->translate('Plan'),
        'icon' => 'fas fa-list-ol',
      ],
      [
        'app' => $this->app,
        'url' => 'calendar?show=leads',
        'title' => $this->app->translate('Calendar'),
        'icon' => 'fas fa-calendar-days',
      ],
    ];
  }

}