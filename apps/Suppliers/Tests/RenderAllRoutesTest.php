<?php declare(strict_types=1);

namespace Hubleto\App\Community\Suppliers\Tests;

use Hubleto\App\Community\Suppliers\Models\Supplier;

final class RenderAllRoutesTest extends \Hubleto\Erp\TestCase
{

  public function testModelCruds(): void
  {
    $this->_testModelCrud(Supplier::class, 'suppliers');
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
