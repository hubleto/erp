<?php

namespace HubletoMain\Cli\Agent\Debug;

class Router extends \HubletoMain\Cli\Agent\Command
{
  public array $releaseConfig = [];

  public function run(): void
  {

    $routeToDebug = (string) ($this->arguments[3] ?? '');

    if (empty($routeToDebug)) {
      $routes = $this->main->router->getRoutes(\ADIOS\Core\Router::HTTP_GET);

      $this->cli->cyan("Available routes (Route -> Controller):\n");
      foreach ($routes as $route => $controller) {
        $this->cli->cyan("  {$route} -> {$controller}\n");
      }
    } else {
      $this->cli->cyan("Debugging route '" . $routeToDebug . "':\n");
      $controller = $this->main->router->findController(\ADIOS\Core\Router::HTTP_GET, $routeToDebug);
      $variables = $this->main->router->extractRouteVariables(\ADIOS\Core\Router::HTTP_GET, $routeToDebug);
      $this->cli->cyan("  - Controller: " . $controller . "\n");
      $this->cli->cyan("  - Variables:\n");
      foreach ($variables as $varName => $varValue) {
        $this->cli->cyan("      {$varName} = {$varValue}\n");
      }
    }

  }
}