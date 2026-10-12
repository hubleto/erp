<?php declare(strict_types=1);

namespace Hubleto\App\Community\PriceList\Tests;

use Hubleto\App\Community\Invoices\Services\PriceCalculator as InvoicesPriceCalculator;
use Hubleto\App\Community\Products\Services\PriceCalculator as ProductsPriceCalculator;
use Hubleto\App\Community\PriceList\Services\PriceCalculator;

/**
 * The five methods of PriceCalculatorInterface must give identical results in PriceList and in the community defaults.
 */
final class PriceCalculatorParityTest extends \Hubleto\Erp\TestCase
{
  public function testParityWithCommunityCalculators(): void
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    /** @var PriceCalculator $mine */
    $mine = $hubleto->getService(PriceCalculator::class);
    /** @var ProductsPriceCalculator $products */
    $products = $hubleto->getService(ProductsPriceCalculator::class);
    /** @var InvoicesPriceCalculator $invoices */
    $invoices = $hubleto->getService(InvoicesPriceCalculator::class);

    $vectors = json_decode(file_get_contents(__DIR__ . '/Data/price-vectors.json'), true)['items'];
    foreach ($vectors as $v) {
      foreach ([$products, $invoices] as $other) {
        $this->assertSame($other->calculateFullPrice($v['unit_price'], $v['amount']), $mine->calculateFullPrice($v['unit_price'], $v['amount']));
        $this->assertSame($other->calculatePriceExcludingVat($v['unit_price'], $v['amount'], $v['discount']), $mine->calculatePriceExcludingVat($v['unit_price'], $v['amount'], $v['discount']));
        $this->assertSame(
          $other->calculatePriceIncludingVat($v['unit_price'], $v['amount'], $v['discount'], $v['vat']),
          $mine->calculatePriceIncludingVat($v['unit_price'], $v['amount'], $v['discount'], $v['vat'])
        );
      }
    }
  }
}
