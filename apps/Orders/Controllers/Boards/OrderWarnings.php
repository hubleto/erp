<?php

namespace Hubleto\App\Community\Orders\Controllers\Boards;

use Hubleto\App\Community\Orders\Models\Order;

class OrderWarnings extends \Hubleto\Erp\Controller
{
  public bool $hideDefaultDesktop = true;

  public function prepareView(): void
  {
    parent::prepareView();

    $warningsTotal = 0;

    $mOrder = $this->getService(Order::class);

    $myOrders = $mOrder->record->prepareReadQuery()
      ->get()
      ->toArray()
    ;

    // open-orders-without-future-plan
    $items = [];

    foreach ($myOrders as $order) {
      $futureActivities = 0;
      foreach ($order['ACTIVITIES'] as $activity) {
        if (strtotime($activity['date_start']) > time()) {
          $futureActivities++;
        }
      }
      if (!$order['is_closed'] && $futureActivities == 0) {
        $items[] = $order;
        $warningsTotal++;
      }
    }

    $warnings['open-orders-without-future-plan'] = [
      "title" => $this->translate('Open orders without future plan'),
      "items" => $items,
    ];
    //

    $this->viewParams['warningsTotal'] = $warningsTotal;
    $this->viewParams['warnings'] = $warnings;

    $this->setView('@Hubleto:App:Community:Orders/Boards/OrderWarnings.twig');
  }

}
