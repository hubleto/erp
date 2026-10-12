<?php

namespace Hubleto\App\Community\PriceLists\Controllers\Api;

use Hubleto\App\Community\PriceLists\Models\Item;

/**
 * Creates, updates or (for an empty value) deletes one fixed price of the matrix.
 * Parameters: idPriceList, idProduct, minQty, value.
 */
class SetMatrixPrice extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $router = $this->router();
    $idPriceList = $router->urlParamAsInteger('idPriceList');
    $idProduct = $router->urlParamAsInteger('idProduct');
    $minQty = $router->urlParamAsFloat('minQty');

    if ($idPriceList <= 0 || $idProduct <= 0) {
      return ['status' => 'error', 'message' => $this->translate('Select a price list and a product.')];
    }

    /** @var Item $mItem */
    $mItem = $this->getModel(Item::class);
    $t = $mItem->table;

    $existing = $mItem->record
      ->where($t . '.id_price_list', $idPriceList)
      ->where($t . '.id_product', $idProduct)
      ->where($t . '.method', Item::METHOD_FIXED)
      ->where($t . '.min_qty', $minQty)
      ->first()
    ;

    try {
      if ($router->urlParamAsString('value', '') === '') {
        if ($existing) $mItem->record->recordDelete((int) $existing->id);
        return ['status' => 'success'];
      }

      $value = $router->urlParamAsFloat('value');
      if ($existing) {
        $mItem->record->recordUpdate(array_merge($existing->getAttributes(), ['value' => $value]));
      } else {
        $mItem->record->recordCreate([
          'id_price_list' => $idPriceList, 'id_product' => $idProduct, 'min_qty' => $minQty,
          'method' => Item::METHOD_FIXED, 'value' => $value,
        ]);
      }
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }

    return ['status' => 'success'];
  }
}
