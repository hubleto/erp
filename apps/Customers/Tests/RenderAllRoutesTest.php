<?php declare(strict_types=1);

namespace Hubleto\App\Community\Customers\Tests;

use PHPUnit\Framework\TestCase;

final class RenderAllRoutesTest extends TestCase
{

  public function testAddCustomer(): void
  {
    $html = \Hubleto\Erp\Loader::getGlobalApp()->renderer()->render('customers/add');
    $this->assertStringContainsString('app-main-title', $html);
    $this->assertStringNotContainsStringIgnoringCase('error', $html);
  }

  public function testSettings(): void
  {
    $html = \Hubleto\Erp\Loader::getGlobalApp()->renderer()->render('customers/settings');
    $this->assertStringContainsString('app-main-title', $html);
    $this->assertStringNotContainsStringIgnoringCase('error', $html);
  }

  public function testCustomerActivities(): void
  {
    $html = \Hubleto\Erp\Loader::getGlobalApp()->renderer()->render('customers/add');
    $this->assertStringContainsString('app-main-title', $html);
    $this->assertStringNotContainsStringIgnoringCase('error', $html);
  }

  public function testCustomerForms(): void
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $mCustomer = $hubleto->getModel(\Hubleto\App\Community\Customers\Models\Customer::class);
    $customers = $mCustomer->record->get()->toArray();
    foreach ($customers as $customer) {
      $html = $hubleto->renderer()->render('customers/' . $customer['id']);
      $this->assertStringContainsString('app-main-title', $html);
      $this->assertStringNotContainsStringIgnoringCase('error', $html);
    }
  }

}
