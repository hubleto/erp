<?php

namespace Hubleto\App\Community\Orders\Controllers\Api;

use Exception;
use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\App\Community\Orders\Models\OrderDeal;
use Hubleto\App\Community\Deals\Models\Deal;

class SetParentDeal extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): array
  {
    $idOrder = $this->router()->urlParamAsInteger("idOrder");
    $idDeal = $this->router()->urlParamAsInteger("idDeal");

    if ($idOrder <= 0 || $idDeal <= 0) {
      return [
        "status" => "failed",
        "error" => "The order or deal not set."
      ];
    }

    /** @var OrderDeal */
    $mOrderDeal = $this->getModel(OrderDeal::class);

    try {
      $mOrderDeal->record->where('id_order', $idOrder)->delete();
      $mOrderDeal->record->create([
        'id_order' => $idOrder,
        'id_deal' => $idDeal,
      ]);
    } catch (Exception $e) {
      return [
        "status" => "failed",
        "error" => $e
      ];
    }

    return [
      "status" => "success",
      "idOrder" => $idOrder,
    ];
  }

}
