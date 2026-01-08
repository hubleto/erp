<?php declare(strict_types=1);

namespace Hubleto\App\Community\Dashboard\Tests;

use Hubleto\App\Community\Dashboards\Models\Dashboard;
use Hubleto\App\Community\Dashboards\Models\Panel;

final class RenderAllRoutesTest extends \Hubleto\Erp\TestCase
{

  public function testCrudRouteForModel(): void
  {
    $this->_testCrudRouteForModel(Dashboard::class, 'dashboards');
  }

  public function testApiRoutes(): void
  {
    $this->_testApiRouteReturnsJson('dashboards/api/sort-panels', ['idDashboard' => 1, 'idPanelsSorted' => [1, 2, 3]]);
    $this->_testApiRouteReturnsJson('dashboards/api/set-panel-width', ['idDashboard' => 1, 'idPanel' => 2, 'width' => 3]);
  }

}
