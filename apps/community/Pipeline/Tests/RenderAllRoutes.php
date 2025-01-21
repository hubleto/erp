<?php

namespace HubletoApp\Community\Pipeline\Tests;

class RenderAllRoutes extends \HubletoMain\Core\AppTest
{

  public function run(): void
  {
    $routes = [
      'pipeline',
    ];

    foreach ($routes as $route) {
      $this->cli->cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
