<?php

namespace Hubleto\App\Community\Orders\Controllers;

use Hubleto\App\Community\Orders\Counter;
use Hubleto\App\Community\Orders\Models\Order;

class OrdersAwaitingInvoice extends \Hubleto\Erp\Controller
{
  public function getBreadcrumbs(): array
  {
    return array_merge(parent::getBreadcrumbs(), [
      [ 'url' => 'orders', 'content' => $this->translate('Orders') ],
      [ 'url' => '', 'content' => $this->translate('Orders awaiting invoice') ],
    ]);
  }

  public function prepareView(): void
  {
    parent::prepareView();

    /** @var Counter */
    $counter = $this->getService(Counter::class);

    $ordersAwaitingInvoice = $counter->ordersAwaitingInvoice();

    /** @var Order */
    $mOrder = $this->getModel(Order::class);

    $orders = $mOrder->record->prepareReadQuery()
      ->whereIn('orders.id', $ordersAwaitingInvoice)
      ->get()
      ?->toArray()
    ;

    $this->viewParams['orders'] = $orders;

    $this->setView('@Hubleto:App:Community:Orders/OrdersAwaitingInvoice.twig');

  }

}
