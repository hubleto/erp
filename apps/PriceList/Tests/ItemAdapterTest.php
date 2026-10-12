<?php declare(strict_types=1);

namespace Hubleto\App\Community\PriceList\Tests;

use Hubleto\App\Community\Orders\Models\Item as OrderItem;
use Hubleto\App\Community\PriceList\Services\ItemAdapterRegistry;

final class ItemAdapterTest extends \Hubleto\Erp\TestCase
{
  public function testBuiltInAdapters(): void
  {
    /** @var ItemAdapterRegistry $registry */
    $registry = \Hubleto\Erp\Loader::getGlobalApp()->getService(ItemAdapterRegistry::class);

    $this->assertTrue($registry->isRegistered(OrderItem::class));
    $adapter = $registry->getAdapter(OrderItem::class);
    foreach (['productField', 'quantityField', 'priceField', 'parent'] as $key) {
      $this->assertArrayHasKey($key, $adapter);
    }
    $this->assertFalse($registry->isRegistered('Not\\An\\ItemModel'));
  }
}
