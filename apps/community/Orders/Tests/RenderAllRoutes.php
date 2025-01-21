<?php

namespace HubletoApp\Community\Orders\Tests;

class RenderAllRoutes extends \HubletoMain\Core\AppTest
{

  public function run(): void
  {
    $routes = [
      'orders',
    ];

    foreach ($routes as $route) {
      $this->cli->cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
