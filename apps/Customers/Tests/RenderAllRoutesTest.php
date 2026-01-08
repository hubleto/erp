<?php declare(strict_types=1);

namespace Hubleto\App\Community\Customers\Tests;

use Hubleto\App\Community\Customers\Models\Customer;
use Hubleto\App\Community\Customers\Models\CustomerDocument;
use Hubleto\App\Community\Customers\Models\Tag;
use Hubleto\App\Community\Customers\Models\CustomerTag;

final class RenderAllRoutesTest extends \Hubleto\Erp\TestCase
{

  public function testCrudRouteForModel(): void
  {
    $this->_testCrudRouteForModel(Customer::class, 'customers');
    $this->_testCrudRouteForModel(CustomerDocument::class, 'customers');
    $this->_testCrudRouteForModel(Tag::class, 'customers');
    $this->_testCrudRouteForModel(CustomerTag::class, 'customers');
  }

  public function testApiRoutes(): void
  {
    $this->_testApiRouteReturnsJson('customers/api/get-customer');
    $this->_testApiRouteReturnsJson('customers/api/log-activity', ['idCustomer' => 1, 'activity' => 'test']);
  }

  // public function testRoutesRendersJson(): void
  // {
  //   $this->testRouteRendersJson('customers/api/get-customer');
  //   $this->testRouteRendersJson('customers/api/log-activity');
  // }

  // public function testCustomerForms(): void
  // {
  //   $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
  //   $mCustomer = $hubleto->getModel(\Hubleto\App\Community\Customers\Models\Customer::class);
  //   $customers = $mCustomer->record->get()->toArray();
  //   foreach ($customers as $customer) {
  //     $html = $hubleto->renderer()->render('customers/' . $customer['id']);
  //     $this->assertStringContainsString('app-main-title', $html);
  //     $this->assertStringNotContainsStringIgnoringCase('error', $html);
  //   }
  // }

}
