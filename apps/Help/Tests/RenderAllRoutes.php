<?php

namespace HubletoApp\Community\Help\Tests;

class RenderAllRoutes extends \Hubleto\Framework\AppTest
{
  public function run(): void
  {
    $routes = [
      'help',
    ];

    foreach ($routes as $route) {
      $this->cli->cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
