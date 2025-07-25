<?php

namespace HubletoApp\Community\Leads\Tests;

class RenderAllRoutes extends \Hubleto\Framework\AppTest
{
  public function run(): void
  {
    $routes = [
      'leads',
      // 'leads/get-calendar-events',
      'leads/archive',
      'leads/api/convert-to-deal',
      'settings/lead-statuses',
    ];

    foreach ($routes as $route) {
      \Hubleto\Terminal::cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
