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
        'url' => 'projects/tasks',
        'title' => $this->app->translate('Assign task to project'),
        'icon' => 'fas fa-check-double',
      ],
      [
        'app' => $this->app,
        'url' => 'projects/orders',
        'title' => $this->app->translate('Assign project to order'),
        'icon' => 'fas fa-check-double',
      ],
      [
        'app' => $this->app,
        'url' => 'projects/milestone',
        'title' => $this->app->translate('Assign task to milestone'),
        'icon' => 'fas fa-check-double',
      ],
      [
        'app' => $this->app,
        'url' => 'projects/monthly-summary',
        'title' => $this->app->translate('Monthly summary'),
        'icon' => 'fas fa-chart-bar',
      ],
      [
        'app' => $this->app,
        'url' => 'projects/calendar?show=projects',
        'title' => $this->app->translate('Calendar'),
        'icon' => 'fas fa-calendar',
      ],
    ];
  }

}