<?php

namespace HubletoApp\Community\Deals\Tests;

class RenderAllRoutes extends \Hubleto\Framework\AppTest
{
  public function run(): void
  {
    $routes = [
      'deals',
      // 'deals/get-calendar-events',
      'deals/archive',
      'deals/change-pipeline',
      'deals/change-pipeline-step',
      'settings/deal-statuses',
    ];

    foreach ($routes as $route) {
      \Hubleto\Terminal::cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
