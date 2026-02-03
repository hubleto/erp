<?php

namespace Hubleto\App\Community\Orders\Controllers;

use Hubleto\App\Community\Orders\Counter;
use Hubleto\App\Community\Orders\Models\Order;

class MissingItemsInPeriodicalOrders extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'orders', 'content' => $this->translate('Orders') ],
      [ 'url' => '', 'content' => $this->translate('Missing items in periodical orders') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Counter */
    $counter = $this->getService(Counter::class);

    $periodicalOrdersMissingItems = $counter->periodicalOrdersMissingItems();

    /** @var Order */
    $mOrder = $this->getModel(Order::class);

    $orders = $mOrder->record->prepareReadQuery()
      ->whereIn('orders.id', $periodicalOrdersMissingItems)
      ->get()
      ?->toArray()
    ;

    $tmp = $mOrder->record
      ->selectRaw('
        orders.id,
        ifnull(max(orders_items.date_due), "2000-01-01") as last_item_date_due
      ')
      ->leftJoin('orders_items', 'orders_items.id_order', '=', 'orders.id')
      ->whereRaw('orders.payment_period > 0')
      ->groupBy('orders.id')
      ->get()
      ?->toArray()
    ;

    $this->viewParams['orders'] = $orders;
    $this->viewParams['tmp'] = $tmp;

    $this->setView('@Hubleto:App:Community:Orders/MissingItemsInPeriodicalOrders.twig');

  }

}
