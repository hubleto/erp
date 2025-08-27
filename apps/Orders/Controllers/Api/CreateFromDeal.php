<?php

namespace HubletoApp\Community\Orders\Controllers\Api;

use Exception;
use HubletoApp\Community\Orders\Models\Order;
use HubletoApp\Community\Orders\Models\OrderDeal;
use HubletoApp\Community\Deals\Models\Deal;

class CreateFromDeal extends \HubletoMain\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    if (!$this->getRouter()->isUrlParam("idDeal")) {
      return [
        "status" => "failed",
        "error" => "The deal for converting was not set"
      ];
    }

    $idDeal = $this->getRouter()->urlParamAsInteger("idDeal");

    $mDeal = $this->getService(Deal::class);
    $mOrder = $this->getService(Order::class);
    $mOrderDeal = $this->getService(OrderDeal::class);
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
