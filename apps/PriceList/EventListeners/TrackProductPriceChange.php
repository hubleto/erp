<?php

namespace Hubleto\App\Community\PriceList\EventListeners;

use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\PriceList\Models\History;
use Hubleto\App\Community\PriceList\Models\Item;
use Hubleto\App\Community\PriceList\Models\PriceList;
use Hubleto\App\Community\PriceList\Services\HistoryWriter;
use Hubleto\Framework\Interfaces\ModelInterface;

/**
 * When the sales price of a product changes, records it in the history of the price lists that depend on it
 * (lists based on product sales prices and prices with the base "product sales price").
 */
class TrackProductPriceChange extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{
  public function onModelAfterUpdate(ModelInterface $model, array $originalRecord, array $savedRecord): void
  {
    if (!($model instanceof Product)) return;

    $oldPrice = (float) ($originalRecord['sales_price'] ?? 0);
    $newPrice = (float) ($savedRecord['sales_price'] ?? 0);
    if ($oldPrice === $newPrice) return;

    try {
      /** @var PriceList $mPriceList */
      $mPriceList = $this->getModel(PriceList::class);
      /** @var Item $mItem */
      $mItem = $this->getModel(Item::class);
      /** @var HistoryWriter $historyWriter */
      $historyWriter = $this->getService(HistoryWriter::class);

      $idLists = $mPriceList->record
        ->where($mPriceList->table . '.status', PriceList::STATUS_ACTIVE)
        ->where($mPriceList->table . '.basis', PriceList::BASIS_PRODUCT_SALES_PRICE)
        ->pluck('id')
        ->toArray()
      ;

      $idListsFromItems = $mItem->record
        ->where($mItem->table . '.id_product', (int) $savedRecord['id'])
        ->where($mItem->table . '.base', Item::BASE_PRODUCT_SALES_PRICE)
        ->where($mItem->table . '.method', '!=', Item::METHOD_FIXED)
        ->pluck('id_price_list')
        ->toArray()
      ;

      foreach (array_unique(array_merge($idLists, $idListsFromItems)) as $idPriceList) {
        $historyWriter->write(
          (int) $idPriceList,
          History::EVENT_SOURCE_PRICE_CHANGED,
          $this->translate('Sales price of a product changed'),
          ['id_product' => (int) $savedRecord['id'], 'old_value' => $oldPrice, 'new_value' => $newPrice],
          'listener'
        );
      }
    } catch (\Throwable $e) {
      // tracking must never block saving of a product
    }
  }
}
