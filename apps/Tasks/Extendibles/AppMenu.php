<?php

namespace Hubleto\App\Community\Tasks\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'tasks',
        'title' => $this->app->translate('Tasks'),
        'icon' => 'fas fa-list-check',
      ],
      [
        'app' => $this->app,
        'url' => 'tasks/todo',
        'title' => $this->app->translate('Todo'),
        'icon' => 'fas fa-receipt',
      ],
      [
        'app' => $this->app,
        'url' => 'calendar?show=tasks',
        'title' => $this->app->translate('Calendar'),
        'icon' => 'fas fa-calendar-days',
      ],
    ];
  }

}