<?php declare(strict_types=1);

namespace Hubleto\App\Community\PriceList\Tests;

use Hubleto\App\Community\PriceList\Services\PriceCalculator;

/**
 * The SQL builders must produce the expressions used by the Orders record managers.
 */
final class SqlExpressionTest extends \Hubleto\Erp\TestCase
{
  public function testExpressionsEqualOrdersAggregates(): void
  {
    /** @var PriceCalculator $calc */
    $calc = \Hubleto\Erp\Loader::getGlobalApp()->getService(PriceCalculator::class);

    $excl = 'ifnull(orders_items.unit_price, 0) * ifnull(orders_items.amount, 0) * (1 - ifnull(orders_items.discount, 0)/100)';
    $this->assertSame($excl, $calc->sqlItemExclVat('orders_items'));
    $this->assertSame($excl . ' * (1 + ifnull(orders_items.vat, 0)/100)', $calc->sqlItemInclVat('orders_items'));
    $this->assertStringContainsString('ifnull(t.quantity, 0)', $calc->sqlItemExclVat('t', ['amount' => 'quantity']));
  }
}
