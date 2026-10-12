<?php declare(strict_types=1);

namespace Hubleto\App\Community\PriceList\Tests;

use Hubleto\App\Community\PriceList\Services\VatCalculator;

final class VatCalculatorTest extends \Hubleto\Erp\TestCase
{
  protected function vat(): VatCalculator
  {
    /** @var VatCalculator $vat */
    $vat = \Hubleto\Erp\Loader::getGlobalApp()->getService(VatCalculator::class);
    return $vat;
  }

  public function testArithmetic(): void
  {
    $vat = $this->vat();
    $this->assertEqualsWithDelta(20, $vat->vatFromBase(100, 20), 1e-9);
    $this->assertEqualsWithDelta(100, $vat->baseFromGross(120, 20), 1e-9);
    $this->assertEqualsWithDelta(20, $vat->vatFromGross(120, 20), 1e-9);
    $this->assertEqualsWithDelta(120, $vat->grossFromBase(100, 20), 1e-9);
  }

  public function testSummaryGroupsByRate(): void
  {
    $summary = $this->vat()->summarize([
      ['vat' => 20, 'price_excl_vat' => 100, 'price_vat' => 20],
      ['vat' => 20, 'price_excl_vat' => 50],
      ['vat' => 5, 'price_excl_vat' => 10],
      ['vat' => 5, 'price_excl_vat' => 99, '_toBeDeleted_' => true],
    ]);
    $this->assertCount(2, $summary);
    $this->assertEqualsWithDelta(150, $summary[0]['base'], 1e-9);
    $this->assertEqualsWithDelta(30, $summary[0]['vat'], 1e-9);
  }

  public function testExplicitVatWinsOverEverything(): void
  {
    $rate = $this->vat()->resolveRate(['vat' => 0, 'idProduct' => 1]);
    $this->assertSame(0.0, $rate['percent']);
    $this->assertSame('explicit', $rate['source']);
  }
}
