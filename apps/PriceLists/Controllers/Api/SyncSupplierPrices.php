<?php

namespace Hubleto\App\Community\PriceLists\Controllers\Api;

use Hubleto\App\Community\Products\Models\ProductSupplier;
use Hubleto\App\Community\PriceLists\Models\Assignment;
use Hubleto\App\Community\PriceLists\Models\Item;

/**
 * Copies purchase prices of the supplier assigned to a purchase price list into the prices of the list.
 */
class SyncSupplierPrices extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idPriceList = $this->router()->urlParamAsInteger('idPriceList');

    /** @var Assignment $mAssignment */
    $mAssignment = $this->getModel(Assignment::class);
    $idSupplier = (int) $mAssignment->record
      ->where($mAssignment->table . '.id_price_list', $idPriceList)
      ->where($mAssignment->table . '.id_supplier', '>', 0)
      ->value('id_supplier')
    ;
    if ($idSupplier <= 0) {
      return ['status' => 'error', 'message' => $this->translate('Assign a supplier to the price list first.')];
    }

    /** @var ProductSupplier $mProductSupplier */
    $mProductSupplier = $this->getModel(ProductSupplier::class);
    /** @var Item $mItem */
    $mItem = $this->getModel(Item::class);

    $created = 0;
    $updated = 0;

    foreach ($mProductSupplier->record->where($mProductSupplier->table . '.id_supplier', $idSupplier)->get() as $ps) {
      $existing = $mItem->record
        ->where($mItem->table . '.id_price_list', $idPriceList)
        ->where($mItem->table . '.id_product', (int) $ps->id_product)
        ->where($mItem->table . '.id_supplier', $idSupplier)
        ->first()
      ;

      if ($existing) {
        $mItem->record->recordUpdate(array_merge($existing->getAttributes(), ['method' => Item::METHOD_FIXED, 'value' => (float) $ps->purchase_price]));
        $updated++;
      } else {
        $mItem->record->recordCreate([
          'id_price_list' => $idPriceList, 'id_product' => (int) $ps->id_product, 'id_supplier' => $idSupplier,
          'method' => Item::METHOD_FIXED, 'value' => (float) $ps->purchase_price,
        ]);
        $created++;
      }
    }

    return ['status' => 'success', 'created' => $created, 'updated' => $updated];
  }
}
