<?php declare(strict_types=1);

namespace Hubleto\App\Community\Deals\Tests;

use Hubleto\App\Community\Deals\Models\Deal;

final class RenderAllRoutesTest extends \Hubleto\Erp\TestCase
{

  public function testCrudRouteForModel(): void
  {
    $this->_testCrudRouteForModel(Deal::class, 'deals');
  }

  public function testApiRoutes(): void
  {
    $this->_testApiRouteReturnsJson('deals/api/log-activity', ['idDeal' => 1, 'activity' => 'test']);
    $this->_testApiRouteReturnsJson('deals/api/create-from-lead', ['idLead' => 1]);
    $this->_testApiRouteReturnsJson('deals/api/generate-quotation-pdf', ['idDeal' => 1]);
    $this->_testApiRouteReturnsJson('deals/api/generate-invoice', ['idDeal' => 1]);
    $this->_testApiRouteReturnsJson('deals/api/set-parent-lead', ['idDeal' => 1, 'idLead' => 2]);
  }

}
