<?php declare(strict_types=1);

namespace Hubleto\Erp\Cli\Agent\Debug;

class Router extends \Hubleto\Erp\Cli\Agent\Command
{
  public array $releaseConfig = [];

  public function run(): void
  {

    $routeToDebug = (string) ($this->arguments[3] ?? '');

    if (empty($routeToDebug)) {
      $routes = $this->router()->getRoutes(\Hubleto\Erp\Router::HTTP_GET);

      $this->terminal()->cyan("Available routes (Route -> Controller):\n");
      foreach ($routes as $route => $controller) {
        $this->terminal()->cyan("  {$route} -> {$controller}\n");
      }
    } else {
      $this->terminal()->cyan("Debugging route '" . $routeToDebug . "':\n");
      $controller = $this->router()->findController(\Hubleto\Erp\Router::HTTP_GET, $routeToDebug);
      $variables = $this->router()->extractRouteVariables(\Hubleto\Erp\Router::HTTP_GET, $routeToDebug);
      $this->terminal()->cyan("  - Controller: " . $controller . "\n");
      $this->terminal()->cyan("  - Variables:\n");
      foreach ($variables as $varName => $varValue) {
        $this->terminal()->cyan("      {$varName} = {$varValue}\n");
      }
    }

  }
}
