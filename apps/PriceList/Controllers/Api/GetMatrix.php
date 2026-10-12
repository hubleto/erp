<?php

namespace Hubleto\App\Community\PriceList\Controllers\Api;

use Hubleto\App\Community\PriceList\Models\Item;

/**
 * Data of the price matrix: fixed prices of products of a price list grouped by quantity tier.
 * Parameters: idPriceList.
 */
class GetMatrix extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idPriceList = $this->router()->urlParamAsInteger('idPriceList');
    if ($idPriceList <= 0) return ['status' => 'success', 'tiers' => [], 'rows' => []];

    /** @var Item $mItem */
    $mItem = $this->getModel(Item::class);
    $t = $mItem->table;

    $items = $mItem->record
      ->with('PRODUCT')
      ->where($t . '.id_price_list', $idPriceList)
      ->where($t . '.id_product', '>', 0)
      ->where($t . '.method', Item::METHOD_FIXED)
      ->orderBy($t . '.id_product')
      ->orderBy($t . '.min_qty')
      ->get()
    ;

    $tiers = [];
    $rows = [];
    foreach ($items as $item) {
      $tier = (string) (float) $item->min_qty;
      $tiers[$tier] = (float) $item->min_qty;

      $idProduct = (int) $item->id_product;
      if (!isset($rows[$idProduct])) {
        $rows[$idProduct] = ['idProduct' => $idProduct, 'productName' => (string) ($item->PRODUCT->name ?? ('#' . $idProduct)), 'prices' => []];
      }
      $rows[$idProduct]['prices'][$tier] = ['id' => (int) $item->id, 'value' => (float) $item->value];
    }

    asort($tiers);

    return ['status' => 'success', 'tiers' => array_values($tiers), 'rows' => array_values($rows)];
  }
}
