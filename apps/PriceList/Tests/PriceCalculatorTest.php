<?php declare(strict_types=1);

namespace Hubleto\App\Community\PriceList\Tests;

use Hubleto\App\Community\PriceList\Services\PriceCalculator;

final class PriceCalculatorTest extends \Hubleto\Erp\TestCase
{
  protected function calculator(): PriceCalculator
  {
    /** @var PriceCalculator $calc */
    $calc = \Hubleto\Erp\Loader::getGlobalApp()->getService(PriceCalculator::class);
    return $calc;
  }

  public static function vectors(): array
  {
    $data = json_decode(file_get_contents(__DIR__ . '/Data/price-vectors.json'), true);
    return array_map(fn($v) => [$v], $data['items']);
  }

  /** @dataProvider vectors */
  public function testInterfaceMethods(array $v): void
  {
    $calc = $this->calculator();
    $this->assertEqualsWithDelta($v['full_price'], $calc->calculateFullPrice($v['unit_price'], $v['amount']), 1e-9);
    $this->assertEqualsWithDelta($v['price_excl_vat'], $calc->calculatePriceExcludingVat($v['unit_price'], $v['amount'], $v['discount']), 1e-9);
    // argument order: discount, then VAT
    $this->assertEqualsWithDelta($v['price_incl_vat'], $calc->calculatePriceIncludingVat($v['unit_price'], $v['amount'], $v['discount'], $v['vat']), 1e-9);
  }

  /** @dataProvider vectors */
  public function testCalculateItem(array $v): void
  {
    $item = $this->calculator()->calculateItem(
      ['unit_price' => $v['unit_price'], 'amount' => $v['amount'], 'discount' => $v['discount'], 'vat' => $v['vat']],
      ['rounding' => 'none']
    );
    $this->assertEqualsWithDelta($v['price_excl_vat'], $item['price_excl_vat'], 1e-9);
    $this->assertEqualsWithDelta($v['price_vat'], $item['price_vat'], 1e-9);
    $this->assertEqualsWithDelta($v['price_incl_vat'], $item['price_incl_vat'], 1e-9);
  }

  public function testTotalsPerItemAndPerRateGroup(): void
  {
    $items = [
      ['unit_price' => 10, 'amount' => 3, 'discount' => 0, 'vat' => 20],
      ['unit_price' => 5.55, 'amount' => 2, 'discount' => 10, 'vat' => 20],
      ['unit_price' => 100, 'amount' => 1, 'discount' => 0, 'vat' => 5],
      ['unit_price' => 50, 'amount' => 1, 'discount' => 0, 'vat' => 5, '_toBeDeleted_' => true],
    ];
    $calc = $this->calculator();

    $perItem = $calc->calculateTotals($items, ['vat_mode' => 'per_item', 'rounding' => 'none']);
    $this->assertEqualsWithDelta(139.99, $perItem['total_excl_vat'], 1e-9);
    $this->assertCount(2, $perItem['vat_summary']);

    $perGroup = $calc->calculateTotals($items, ['vat_mode' => 'per_rate_group', 'rounding' => 'item', 'decimals_item' => 2]);
    $group20 = array_values(array_filter($perGroup['vat_summary'], fn($s) => $s['rate'] == 20))[0];
    $this->assertEqualsWithDelta(8.0, $group20['vat'], 1e-9);
  }

  public function testVatModeAndMargins(): void
  {
    $calc = $this->calculator();
    $this->assertEqualsWithDelta(100, $calc->convertVatMode(120, 20, true, false), 1e-9);
    $this->assertEqualsWithDelta(120, $calc->convertVatMode(100, 20, false, true), 1e-9);
    $this->assertEqualsWithDelta(125, $calc->priceFromCostMarkup(100, 25), 1e-9);
    $this->assertEqualsWithDelta(100, $calc->priceFromCostMargin(80, 20), 1e-9);
    $this->expectException(\InvalidArgumentException::class);
    $calc->priceFromCostMargin(80, 100);
  }

  public function testPaidInFull(): void
  {
    $calc = $this->calculator();
    $this->assertTrue($calc->isPaidInFull(100.004, 100.0));
    $this->assertFalse($calc->isPaidInFull(100.01, 100.0));
    $this->assertFalse($calc->isPaidInFull(0, 0));
  }
}
