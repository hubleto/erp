<?php

namespace HubletoApp\Community\Billing\Tests;

class RenderAllRoutes extends \Hubleto\Framework\AppTest
{
  public function run(): void
  {
    $routes = [
      'billing',
    ];

    foreach ($routes as $route) {
      $this->cli->cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
