<?php

namespace Hubleto\App\Community\Orders\Controllers\Api;


use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\App\Community\Orders\Models\Item;

class GetItem extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idItem = $this->router()->urlParamAsInteger("idItem");

    /** @var Order */
    $mItem = $this->getModel(Item::class);
    $item = $mItem->record
      ->where($mItem->table . '.id', $idItem)
      ->with('ORDER')
      ->with('PRODUCT')
      ->first();

    return [
      "status" => "success",
      "Item" => $item,
    ];
  }

}
