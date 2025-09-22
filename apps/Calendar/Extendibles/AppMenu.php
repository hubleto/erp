<?php

namespace Hubleto\App\Community\Calendar\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'calendar',
        'title' => $this->app->translate('Calendar'),
        'icon' => 'fas fa-calendar',
      ],
      [
        'app' => $this->app,
        'url' => 'calendar/share',
        'title' => $this->app->translate('Share'),
        'icon' => 'fas fa-share',
      ],
    ];
  }

}