<?php

namespace Hubleto\App\Community\Warehouses\Controllers\Api;

use Hubleto\App\Community\Products\Models\Product;
use Hubleto\App\Community\Warehouses\Models\Inventory;
use Hubleto\Erp\Controllers\ApiController;

class GetProductInfo extends ApiController
{

  public function renderJson(): array
  {

    $idProduct = $this->router()->urlParamAsInteger('idProduct');

    $mProduct = $this->getModel(Product::class);
    $mInventory = $this->getModel(Inventory::class);

    try {
      $product = $mProduct->record->prepareReadQuery()
        ->where($mProduct->table . '.id', $idProduct)
        ->first();

      $inventory = $mInventory->record
        ->with('LOCATION.WAREHOUSE')
        ->where($mInventory->table . '.id_product', $idProduct)
        ->get();

      $productInfo = [
        'PRODUCT' => $product,
        'INVENTORY' => $inventory,
      ];

      return ['status' => 'success', 'productInfo' => $productInfo];
    } catch (\Throwable $e) {
      return ['status' => 'error', 'message' => $e->getMessage()];
    }
  }

}
