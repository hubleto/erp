<?php

namespace Hubleto\App\Community\Dashboards\Controllers\Api;

use Hubleto\App\Community\Dashboards\Manager;
use Hubleto\App\Community\Dashboards\Controller as DashboardController;

class DescribeBoard extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $urlSlug = $this->router()->urlParamAsString("urlSlug");

    if (empty($urlSlug)) throw new \Exception('Empty urlSlug.');

    /** @var Boards */
    $manager = $this->getService(Manager::class);
    $boards = $manager->getBoards();

    if (!isset($boards[$urlSlug])) throw new \Exception('Board ' . $urlSlug . ' not found.');

    $board = $boards[$urlSlug];

    if (!isset($board['controllerClass']) || empty($board['controllerClass'])) throw new \Exception('Cannot load board description for ' . $urlSlug . '.');

    $controllerClass = $board['controllerClass'] ?? '';
    if (!class_exists($controllerClass)) throw new \Exception('Invalid description loader for ' . $urlSlug . '.');

    /** @var DashboardController */
    $controller = $this->getService($controllerClass);

    return [
      'options' => $controller->getConfigurationOptionsDescription(),
    ];
  }
}
