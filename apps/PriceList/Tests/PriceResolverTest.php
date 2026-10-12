<?php declare(strict_types=1);

namespace Hubleto\App\Community\PriceList\Tests;

use Hubleto\App\Community\PriceList\Services\PriceResolver;

final class PriceResolverTest extends \Hubleto\Erp\TestCase
{
  public function testResultHasDocumentedKeys(): void
  {
    /** @var PriceResolver $resolver */
    $resolver = \Hubleto\Erp\Loader::getGlobalApp()->getService(PriceResolver::class);

    $result = $resolver->resolve(['idProduct' => -1, 'qty' => 1]);

    $keys = ['unitPrice', 'unitPriceExclVat', 'unitPriceInclVat', 'basePrice', 'discountPercent', 'vatPercent', 'pricesIncludeVat',
      'idCurrency', 'idPriceList', 'idItem', 'appliedRuleIds', 'marginFloorHit', 'floorPrice', 'source', 'trace'];
    foreach ($keys as $key) $this->assertArrayHasKey($key, $result, $key);

    $this->assertContains($result['source'], ['PRICE_LIST_ITEM', 'DERIVED', 'PRODUCT_FALLBACK', 'NONE']);
    $this->assertIsArray($result['trace']);
  }

  public function testUnknownProductHasNoPrice(): void
  {
    /** @var PriceResolver $resolver */
    $resolver = \Hubleto\Erp\Loader::getGlobalApp()->getService(PriceResolver::class);
    $result = $resolver->resolve(['idProduct' => -1, 'idPriceListHint' => -1]);
    $this->assertSame('NONE', $result['source']);
  }
}
