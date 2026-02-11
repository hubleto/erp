<?php

namespace Hubleto\App\Community\Projects\Extendibles;

class AppMenu extends \Hubleto\Framework\Extendible
{
  public function getItems(): array
  {
    return [
      [
        'app' => $this->app,
        'url' => 'projects',
        'title' => $this->app->translate('Projects'),
        'icon' => 'fas fa-diagram-project',
      ],
      [
        'app' => $this->app,
        'url' => 'projects/milestones',
        'title' => $this->app->translate('Milestones'),
        'icon' => 'fas fa-calendar-check',
      ],
      [
        'app' => $this->app,
        'url' => 'projects/monthly-summary',
        'title' => $this->app->translate('Monthly summary'),
        'icon' => 'fas fa-chart-bar',
      ],
    ];
  }

}