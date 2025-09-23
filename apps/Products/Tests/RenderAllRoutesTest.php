<?php declare(strict_types=1);

namespace Hubleto\App\Community\Products\Tests;

use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\Products\Models\ProductSupplier;
use Hubleto\App\Community\Products\Models\Group;

final class RenderAllRoutesTest extends \Hubleto\Erp\TestCase
{

  public function testModelCruds(): void
  {
    $this->_testModelCrud(Product::class, 'products');
    // $this->_testModelCrud(ProductSupplier::class, 'products/suppliers');
    $this->_testModelCrud(Group::class, 'products/groups');
  }

  // public function testRoutesContainAppMainTitle(): void
  // {
  //   $this->_testRouteContainsAppMainTitle('customers/tags');
  // }

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
