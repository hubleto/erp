<?php

namespace Hubleto\App\Community\Orders\Controllers\Api;

use Exception;
use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\App\Community\Orders\Models\OrderDeal;
use Hubleto\App\Community\Deals\Models\Deal;

class CreateFromDeal extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    if (!$this->router()->isUrlParam("idDeal")) {
      return [
        "status" => "failed",
        "error" => "The deal for converting was not set"
      ];
    }

    $idDeal = $this->router()->urlParamAsInteger("idDeal");

    /** @var Deal */
    $mDeal = $this->getModel(Deal::class);

    /** @var Order */
    $mOrder = $this->getModel(Order::class);

    /** @var OrderDeal */
    $mOrderDeal = $this->getModel(OrderDeal::class);

    $deal = null;

    try {
      $deal = $mDeal->record->prepareReadQuery()->where($mDeal->table . ".id", $idDeal)->first();
      if (!$deal) {
        throw new Exception("Deal was not found.");
      }

      $order = $mOrder->record->recordCreate([
        "id_customer" => $deal->id_customer,
        "title" => $deal->title,
        "identifier" => $deal->identifier,
        "id_owner" => $deal->id_owner,
        "id_manager" => $deal->id_manager,
      ]);

      $mOrderDeal->record->recordCreate([
        "id_order" => $order['id'],
        "id_deal" => $deal->id,
      ]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idOrder" => $order['id'],
      "title" => str_replace(" ", "+", (string) $order['title'])
    ];
  }

}
