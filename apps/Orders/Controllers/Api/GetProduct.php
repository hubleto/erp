<?php

namespace Hubleto\App\Community\Orders\Controllers\Api;


use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\App\Community\Orders\Models\OrderProduct;

class GetProduct extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idOrderProduct = $this->router()->urlParamAsInteger("idOrderProduct");

    /** @var Order */
    $mOrderProduct = $this->getModel(OrderProduct::class);
    $orderProduct = $mOrderProduct->record
      ->where($mOrderProduct->table . '.id', $idOrderProduct)
      ->with('ORDER')
      ->with('PRODUCT')
      ->first();

    return [
      "status" => "success",
      "orderProduct" => $orderProduct,
    ];
  }

}
