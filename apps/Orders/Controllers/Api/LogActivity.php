<?php

namespace Hubleto\App\Community\Orders\Controllers\Api;


use Hubleto\App\Community\Orders\Models\Order;
use Hubleto\App\Community\Orders\Models\OrderActivity;

class LogActivity extends \Hubleto\Erp\Controllers\ApiController
{
  public function renderJson(): ?array
  {
    $idOrder = $this->router()->urlParamAsInteger("idOrder");
    $activity = $this->router()->urlParamAsString("activity");
    if ($idOrder > 0 && $activity != '') {
      /** @var Order */
      $mOrder = $this->getModel(Order::class);
      $order = $mOrder->record->find($idOrder)->first()?->toArray();

      if ($order && $order['id'] > 0) {
        $mOrderActivity = $this->getService(OrderActivity::class);
        $mOrderActivity->record->recordCreate([
          'id_order' => $idOrder,
          'subject' => $activity,
          'date_start' => date('Y-m-d'),
          'time_start' => date('H:i:s'),
          'all_day' => true,
          'completed' => true,
          'id_owner' => $this->getService(\Hubleto\Framework\AuthProvider::class)->getUserId(),
        ]);
      }
    }

    return [
      "status" => "success",
      "idOrder" => $idOrder,
    ];
  }

}
