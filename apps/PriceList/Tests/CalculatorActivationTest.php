<?php declare(strict_types=1);

namespace Hubleto\App\Community\PriceList\Tests;

use Hubleto\App\Community\Orders\Models\Item as OrderItem;
use Hubleto\App\Community\PriceList\Services\PriceCalculator;

/**
 * With the config key priceCalculatorService set, Orders must compute item prices with PriceList;
 * without it (or when the class does not exist), with its default calculator.
 */
final class CalculatorActivationTest extends \Hubleto\Erp\TestCase
{
  protected const CONFIG_PATH = 'apps/Hubleto\\App\\Community\\Orders/priceCalculatorService';

  public function testOrdersUsesConfiguredCalculator(): void
  {
    $hubleto = \Hubleto\Erp\Loader::getGlobalApp();
    $original = $hubleto->config()->getAsString(self::CONFIG_PATH, '');

    try {
      /** @var OrderItem $mItem */
      $mItem = $hubleto->getModel(OrderItem::class);

      $hubleto->config()->save(self::CONFIG_PATH, PriceCalculator::class);
      $this->assertInstanceOf(PriceCalculator::class, $mItem->getPriceCalculatorService());

      $hubleto->config()->save(self::CONFIG_PATH, 'Missing\\Calculator');
      $this->assertNotInstanceOf(PriceCalculator::class, $mItem->getPriceCalculatorService());

      $hubleto->config()->save(self::CONFIG_PATH, '');
      $this->assertNotInstanceOf(PriceCalculator::class, $mItem->getPriceCalculatorService());
    } finally {
      $hubleto->config()->save(self::CONFIG_PATH, $original);
    }
  }
}
