<?php

namespace HubletoApp\Community\Desktop\Tests;

class RenderAllRoutes extends \Hubleto\Framework\AppTest
{
  public function run(): void
  {
    $routes = [
      '',
    ];

    foreach ($routes as $route) {
      $this->cli->cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
