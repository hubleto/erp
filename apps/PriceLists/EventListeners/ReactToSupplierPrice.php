<?php

namespace Hubleto\App\Community\PriceLists\EventListeners;

use Hubleto\App\Community\Products\Models\ProductSupplier;
use Hubleto\App\Community\PriceLists\Models\Assignment;
use Hubleto\App\Community\PriceLists\Models\History;
use Hubleto\App\Community\PriceLists\Models\Item;
use Hubleto\App\Community\PriceLists\Models\PriceList;
use Hubleto\App\Community\PriceLists\Services\HistoryWriter;
use Hubleto\App\Community\PriceLists\Services\Settings;
use Hubleto\Framework\Interfaces\ModelInterface;

/**
 * When the purchase price of a product changes at a supplier, records it in price lists that depend on cost
 * and (optionally, setting syncSupplierPrices) updates the purchase price list of the supplier.
 */
class ReactToSupplierPrice extends \Hubleto\Framework\EventListener implements \Hubleto\Framework\Interfaces\EventListenerInterface
{
  public function onModelAfterCreate(ModelInterface $model, array $savedRecord): void
  {
    if (!($model instanceof ProductSupplier)) return;
    $this->react((int) ($savedRecord['id_product'] ?? 0), (int) ($savedRecord['id_supplier'] ?? 0), 0.0, (float) ($savedRecord['purchase_price'] ?? 0));
  }

  public function onModelAfterUpdate(ModelInterface $model, array $originalRecord, array $savedRecord): void
  {
    if (!($model instanceof ProductSupplier)) return;
    $oldPrice = (float) ($originalRecord['purchase_price'] ?? 0);
    $newPrice = (float) ($savedRecord['purchase_price'] ?? 0);
    if ($oldPrice === $newPrice) return;
    $this->react((int) ($savedRecord['id_product'] ?? 0), (int) ($savedRecord['id_supplier'] ?? 0), $oldPrice, $newPrice);
  }

  /** Only the id of the deleted record is known here; nothing to do. */
  public function onModelAfterDelete(ModelInterface $model, int $id): void
  {
    if (!($model instanceof ProductSupplier)) return;
  }

  protected function react(int $idProduct, int $idSupplier, float $oldPrice, float $newPrice): void
  {
    if ($idProduct <= 0) return;

    try {
      /** @var Item $mItem */
      $mItem = $this->getModel(Item::class);
      /** @var HistoryWriter $historyWriter */
      $historyWriter = $this->getService(HistoryWriter::class);
      /** @var Settings $settings */
      $settings = $this->getService(Settings::class);

      // cost-based prices of the product
      $idLists = $mItem->record
        ->where($mItem->table . '.id_product', $idProduct)
        ->where(function ($q) use ($mItem) {
          $q->whereIn($mItem->table . '.method', [Item::METHOD_MARKUP_ON_COST, Item::METHOD_MARGIN_ON_COST])
            ->orWhere($mItem->table . '.base', Item::BASE_PRODUCT_COST);
        })
        ->pluck('id_price_list')
        ->toArray()
      ;

      foreach (array_unique($idLists) as $idPriceList) {
        $historyWriter->write((int) $idPriceList, History::EVENT_SOURCE_PRICE_CHANGED, $this->translate('Purchase price of a product changed'), ['id_product' => $idProduct, 'old_value' => $oldPrice, 'new_value' => $newPrice], 'listener');
      }

      // synchronisation of the purchase price list of the supplier
      if ($idSupplier > 0 && $settings->getBool('syncSupplierPrices')) {
        /** @var Assignment $mAssignment */
        $mAssignment = $this->getModel(Assignment::class);
        /** @var PriceList $mPriceList */
        $mPriceList = $this->getModel(PriceList::class);

        $idPurchaseList = (int) $mAssignment->record
          ->join($mPriceList->table, $mPriceList->table . '.id', '=', $mAssignment->table . '.id_price_list')
          ->where($mAssignment->table . '.id_supplier', $idSupplier)
          ->where($mPriceList->table . '.type', PriceList::TYPE_PURCHASE)
          ->value($mAssignment->table . '.id_price_list')
        ;

        if ($idPurchaseList > 0) {
          $existing = $mItem->record
            ->where($mItem->table . '.id_price_list', $idPurchaseList)
            ->where($mItem->table . '.id_product', $idProduct)
            ->where($mItem->table . '.id_supplier', $idSupplier)
            ->first()
          ;
          if ($existing) {
            $mItem->record->recordUpdate(array_merge($existing->toArray(), ['value' => $newPrice, 'method' => Item::METHOD_FIXED]));
          } else {
            $mItem->record->recordCreate(['id_price_list' => $idPurchaseList, 'id_product' => $idProduct, 'id_supplier' => $idSupplier, 'method' => Item::METHOD_FIXED, 'value' => $newPrice]);
          }
        }
      }
    } catch (\Throwable $e) {
      // reacting to supplier prices must never block saving of the product supplier
    }
  }
}
