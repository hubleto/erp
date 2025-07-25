<?php

namespace HubletoApp\Community\Documents\Tests;

class RenderAllRoutes extends \Hubleto\Framework\AppTest
{
  public function run(): void
  {
    $routes = [
      'documents',
    ];

    foreach ($routes as $route) {
      \Hubleto\Terminal::cyan("Rendering route '{$route}'.\n");
      $this->main->render($route);
    }
  }

}
